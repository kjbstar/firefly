<?php
use Carbon\Carbon as Carbon;

class SlowController extends BaseController
{

    /**
     * Try to predict expenses inspired by Lilian Bosch' excellent
     * presentation on slow technology.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {

        /*
         * These are the starting variables. Some components,
         * my main account and some dates and times.
         */
        $components = Component::whereIn('id', [1734, 1595, 1736, 1770])->get();
        $account = Account::find(1);

        // dates and times:
        $predictionStart = Setting::getSetting('predictionStart');
        $search = new Carbon($predictionStart->value);
        $start = new Carbon;
        $end = clone $start;
        $end->endOfMonth();
        $timeLeft = $start->diffInHours($end);

        // contains all the data for the page:
        $data = [];

        // some variables we may use later on.
        $balance = $account->balanceOnDate($start);


        /*
         * Loop each component and do some nifty calculations on them.
         */
        foreach ($components as $component) {
            /*
             * Prepare the array:
             */
            $current = ['id'  => $component->id, 'name' => $component->name,
                        'sum' => 0, 'hours' => 0];

            /*
             * Get the transactions
             */
            $transactionQuery = $component->transactions()->expenses()
                ->afterDate($search);
            $transactions = $transactionQuery->get();
            /*
             * date/time difference object for one transaction to the next.
             */
            $date = clone $start;
            $date->startOfMonth();
            /*
             * Loop the transactions.
             */
            foreach ($transactions as $transaction) {
                // calculate and save the sum of the amount
                $current['sum'] += floatval($transaction->amount) * -1;

                // calc and save the sum of the hours (difference)
                $diff = $date->diffInHours($transaction->date);
                $current['hours'] += $diff;
                $date = $transaction->date;
            }
            /*
             * Now that we have the sum of both, get some calculations going.
             */
            $count = $transactionQuery->count();

            if ($count > 0) {
                $avgh = $current['hours'] / $count;
                $avgs = $current['sum'] / $count;
            } else {
                $avgh = $current['hours'];
                $avgs = $current['sum'];
            }
            $current['avgh'] = round($avgh);
            $current['avgs'] = floatval($avgs);

            // add some extra variables.
            $current['count'] = round($timeLeft / $avgh);
            $current['amount'] = $current['count'] * $current['avgs'];
            Log::debug('Left: ' . $balance);
            $balance = $balance - $current['amount'];
            $current['spacing'] = str_repeat('&nbsp;', $current['count'] * 4);
            $current['size'] = ($current['count'] - 1) * 50;


            $data[] = $current;
        }

        return View::make('slow.index')->with('data', $data)->with(
            'title', 'Slow'
        )->with('left', $balance);

    }

} 