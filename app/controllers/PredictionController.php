<?php
use Carbon\Carbon as Carbon;

include_once(app_path() . '/helpers/Toolkit.php');

class PredictionController extends BaseController
{
    public function index()
    {

    }

    public function prediction($year = null, $month = null)
    {
        $date = Toolkit::parseDate($year, $month);

        /*
         * - text explaining the idea of an extended prediction
         * - list of days
         *  - per day the expected expense
         *  - the transactions its based on
         *  - the transactions ignored.
         * - also try?
         *  - rent, water, utilities etc (exclude them)
         *      - when once a month AND everything matches?
        */

        // get some settings:
        $predictionStart = Setting::getSetting('predictionStart');
        $frontpageAccounts = Toolkit::getFrontpageAccounts();

        // clone some dates:
        $current = clone $date;
        $end = clone $date;
        $end->endOfMonth();

        // holding array:
        $data = [];

        // loop each day of this month:
        while ($current <= $end) {

            // set some vars for this day of the month:
            $dom = ['date' => $current->format('jS \o\f F Y')];
            $dom['accounts'] = [];

            // loop all accounts:
            foreach ($frontpageAccounts as $account) {
                $cAccount = [];
                $cAccount['name'] = $account->name;

                // get the prediction
                $cAccount['prediction'] = $account->predictOnDate($current);

                // get the transactions:

                $transactions = Auth::user()->transactions()->onDayOfMonth(
                    $current
                )->afterDate($predictionStart->value)->expenses()->fromAccount(
                        $account
                    )->beforeDate($date)->where('ignoreprediction', 0)->get();

                // loop the transactions
                $set = [];
                foreach ($transactions as $transaction) {
                    $tDate = $transaction->date->format('d F Y');
                    $set[$tDate] = isset($set[$tDate])
                        ? $set[$tDate]
                        : ['transactions' => [], 'sum' => 0];
                    $set[$tDate]['transactions'][] = $transaction;
                    $set[$tDate]['sum'] += $transaction->amount;
                }

                $cAccount['transactions'] = $set;
                // get ignored transactions
                $ignored = Auth::user()->transactions()->onDayOfMonth(
                    $current
                )->afterDate($predictionStart->value)->expenses()->fromAccount(
                        $account
                    )->beforeDate($date)->where('ignoreprediction', 1)->get();
                // split for possible modifications
                $cAccount['ignored'] = $ignored;


                // save it:
                $dom['accounts'][] = $cAccount;

            }

            $current->addDay();
            $data[] = $dom;

        }

        return View::make('predictions.prediction')->with(
            'title', 'Predictions for ' . $date->format('F Y')
        )->with('data', $data);
    }
}