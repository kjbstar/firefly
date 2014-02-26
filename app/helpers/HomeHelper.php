<?php
include_once(app_path() . '/helpers/Toolkit.php');


use Carbon\Carbon as Carbon;

/**
 * Class HomeHelper
 */
class HomeHelper
{
    /**
     * Returns a list of active accounts for a given month to be used on
     * the home page.
     *
     * @param Carbon $date
     *
     * @return array
     */
    public static function homeAccountList(Carbon $date)
    {
        $start = clone $date;
        $start->startOfMonth();
        $query = Auth::user()->accounts()->remember('homeAccountList', 1440)
            ->notHidden()->get();
        $accounts = [];

        foreach ($query as $account) {
            $url = URL::Route(
                'accountoverview',
                [$account->id, $date->format('Y'), $date->format('m')]
            );

            $entry = [];
            $entry['name'] = $account->name;
            $entry['url'] = $url;
            $entry['current'] = $account->balanceOnDate($date);
            $accounts[] = $entry;
        }

        unset($query, $entry);

        return $accounts;
    }

    /**
     * Display a month's overview of balance history.
     *
     * @param int $year
     * @param int $month
     *
     * @return string
     */
    public static function homeAccountChart($year, $month)
    {
        $realDay = new Carbon; // for the prediction.
        $start = Toolkit::parseDate($year, $month);
        $start->startOfMonth();
        $end = clone $start;
        $end->endOfMonth();
        $start->subDay(); // also last day of previous month

        // are we predicting for a month that has not started yet?
        $futureMonth = $realDay < $start;


        // get the user's front page accounts:
        $accounts = Toolkit::getFrontpageAccounts();

        // create chart:
        $chart = App::make('gchart');
        $chart->addColumn('Day of the month', 'date');

        // create chart & columns for each chart:
        foreach ($accounts as $account) {
            // column for account X:
            $c = $chart->addColumn($account->name . ' balance', 'number');
            $account->balance = $account->balanceOnDate($start);
            $account->balanceMost = $account->balance;
            $account->balanceLeast = $account->balance;
            $chart->addCertainty($c); // whether or not we're certain
            $chart->addInterval($c); // interval cheapest day $cheap
            $chart->addInterval($c); // interval most expensive day. $max

            // column for original prediction of account X:
            $chart->addColumn(
                $account->name . ' original prediction', 'number'
            );
        }

        // loop for each day of the month:
        $current = clone $start;
        while ($current <= $end) {
            // add the current date:
            $row = [];
            $row[] = clone $current;

            // now start generating numbers for each account:
            $todayOrPast = $current < $realDay;
            $future = !$todayOrPast;
            $fom = $current->format('d') == '1'; // first of month

            foreach ($accounts as $account) {
                if ($todayOrPast) {
                    // simply get the current balance, put it in chart.
                    $account->balance = $account->balanceOnDate($current);

                    // update the least/most balances:
                    $account->balanceMost = $account->balance;
                    $account->balanceLeast = $account->balance;

                    $row[] = $account->balance;
                    $row[] = true; // certainty
                    // we ignore the rest
                    $row[] = null; // cheapest
                    $row[] = null; // expensive
                }
                if ($future) {
                    // get a prediction:
                    Log::debug('Balance just now: ' . $account->balance);
                    $prediction = $account->predictOnDate($current);

                    // set the balances:
                    // update values:
                    $account->balance -= $prediction['prediction'];
                    $account->balanceLeast -= $prediction['least'];
                    $account->balanceMost -= $prediction['most'];

                    // add balance - predictions and certainty
                    $row[] = $account->balance;
                    $row[] = false; // certainty
                    $row[] = $account->balanceLeast;
                    $row[] = $account->balanceMost;


                }
                $row[] = null; // original prediction

            }


            $chart->addRowArray($row);
            $current->addDay();
        }


        $chart->generate();

        return $chart->getData();

    }

    /**
     * This method returns the JSON required to render a gauge. The gauge
     * will display the (predicted) balance for the date entered. Subsequently,
     * rendered on a HTML page this gives the user a visual of the (predicted)
     * state of the day in question.
     *
     *
     * @param Carbon $date
     */
    public static function homeGauge(Carbon $date)
    {
        $chart = App::make('gchart');
        $chart->addColumn('Name', 'string');
        $chart->addColumn('Value', 'number');

        $balance = 0;
        $accounts = Toolkit::getFrontpageAccounts();
        $title = $date->format('d M');

        $today = new Carbon;
        $today->startOfDay();
        if ($date <= $today) {
            foreach ($accounts as $account) {
                $balance += $account->balanceOnDate($date);
            }
        } else {
            // grab latest balance
            foreach ($accounts as $account) {
                $balance += $account->balanceOnDate($today);
            }
            $current = clone $today;
            $current->addDay();
            Log::debug('Start balance before prediction: ' . $balance);
            Log::debug('Current: ' . $current->format('d-M-Y'));
            Log::debug('Date: ' . $date->format('d-M-Y'));
            Log::debug('Today: ' . $today->format('d-M-Y'));
            while ($current <= $date) {
                Log::debug('Try: ' . $current->format('d-M-Y'));
                foreach ($accounts as $account) {
                    $prediction = $account->predictOnDate($current);
                    $balance -= $prediction['prediction'];
                }
                $current->addDay();
            }
        }
        $chart->addRow($title, ['v' => $balance, 'f' => '€ ' . $balance]);
        $chart->generate();

        return $chart->getData();
    }

    public static function componentTable($type, Carbon $date)
    {
        $type = Str::singular($type);
        $year = $date->format('Y');
        $month = $date->format('m');
        $rows = [];
        $empty = ['title' => '(empty ' . $type . ')', 'amount' => 0,
                  'url'   => URL::Route('empty' . $type, [$year, $month])];

        // got to find them all!
        $query = Auth::user()->transactions()->hasComponentType($type)->inMonth(
            $date
        );

        if ($type == 'budget') {
            $query->expenses();
        }
        $transactions = $query->get();

        $ids = []; // we need dis

        foreach ($transactions as $t) {
            $component = $t->getComponentByType($type);

            // count for empty ones:
            if (is_null($component)) {
                $empty['amount'] += $t->amount;
                continue;
            }
            $ids[] = $component->id;
            // object already exists for table:
            if (isset($rows[$component->id])) {
                $rows[$component->id]['amount'] += floatval($t->amount);
                continue;
            }
            // create object:
            $url = URL::Route(
                $type . 'overview', [$component->id, $year, $month]
            );

            $c = ['url'    => $url, 'title' => $component->name,
                  'amount' => floatval($t->amount)];
            $rows[$component->id] = $c;
        }
        // get the limits we might have for this month:
        $limits = Limit::whereIn('component_id', $ids)->inMonth($date)->get();
        foreach ($limits as $limit) {
            $id = $limit->component_id;
            $info = ['over'  => false, 'amount' => $limit->amount,
                     'spent' => $rows[$id]['amount'] * -1];
            if ($limit->amount < ($info['spent'])) {
                $info['over'] = true;
            }
            $rows[$id]['limit'] = $info;
        }

        // add empty one
        if ($empty['amount'] != 0) {
            $rows[] = $empty;
        }


        // sort:
        $amount = [];
        foreach ($rows as $key => $row) {
            $amount[$key] = $row['amount'];
        }
        array_multisort($amount, SORT_ASC, $rows);
        if ($type == 'budget') {
            $view = View::make('tables.budget')->with('rows', $rows);
        } else {
            $view = View::make('tables.component')->with('rows', $rows);
        }

        return $view->render();
    }

    public static function transactionTable(Carbon $date)
    {
        $rows = Auth::user()->transactions()->orderBy('date', 'DESC')->inMonth(
            $date
        )->orderBy('id', 'DESC')->take(25)->get();
        $view = View::make('tables.transactions')->with('rows', $rows);

        return $view->render();
    }

    public static function transferTable(Carbon $date)
    {
        $rows = Auth::user()->transfers()->orderBy('date', 'DESC')->inMonth(
            $date
        )->orderBy('id', 'DESC')->take(25)->get();
        $view = View::make('tables.transfers')->with('rows', $rows);

        return $view->render();
    }

    public static function predictionTable(Carbon $date)
    {
        // start at the first of the month.
        // predict the outcome of that day
        // and the actual amount.
        $realDay = new Carbon;
        $accounts = Toolkit::getFrontpageAccounts();

        $eom = clone $date;
        $eom = $eom->endOfMonth();
        $current = clone $date;
        $rows = [];

        // start balance: first day is "base" for predictions.
        $start = 0;
        foreach ($accounts as $a) {
            $start += $a->balanceOnDate($date);
        }
        $latest = $start;
        while ($current <= $eom) {
            $entry = [];
            $entry['date'] = clone $current;
            $actual = 0;

            if ($current <= $realDay) {
                foreach ($accounts as $a) {
                    $actual += $a->balanceOnDate($current);
                }
                $entry['actual'] = $actual;
                $latest = $actual;
                // predict from start:
                foreach ($accounts as $a) {
                    $pred = $a->predictOnDate($current);
                    $start -= $pred['prediction'];
                }
                $entry['prediction'] = $start;
            } else {

                // start with latest balance ($latest).
                foreach ($accounts as $a) {
                    $actual += $a->balanceOnDate($current);
                    // substract prediction:
                    $pred = $a->predictOnDate($current);
                    $latest -= $pred['prediction'];
                }
                $entry['actual'] = null;
                $entry['prediction'] = $latest;

            }


            $rows[] = $entry;
            $current->addDay();
        }


        $view
            = View::make('tables.predictions')->with('rows', $rows)->with(
            'today', $realDay
        )->with('date',$date);

        return $view->render();
    }

        public static function getAllowance(Carbon $date)
    {
        // default values and array
        $defaultAllowance = Setting::getSetting('defaultAllowance');
        $specificAllowance = Auth::user()->settings()->where(
            'name', 'specificAllowance'
        )->where('date', $date->format('Y-m') . '-01')->first();
        $allowance = !is_null($specificAllowance) ? $specificAllowance
            : $defaultAllowance;

        $amount = floatval($allowance->value);
        $allowance = ['amount' => $amount, 'over' => false, 'spent' => 0];
        $days = round(
            (intval($date->format('d')) / intval(
                    $date->format('t')
                )) * 100
        );
        $allowance['days'] = $days;
        // start!
        if ($amount > 0) {
            $spent = floatval(
                    Auth::user()->transactions()->inMonth($date)->expenses()
                        ->where('ignoreallowance', 0)->sum('amount')
                ) * -1;
            $allowance['spent'] = $spent;
            // overspent this allowance:
            if ($spent > $amount) {
                $allowance['over'] = true;
                $allowance['pct'] = round(($amount / $spent) * 100);
            }
            // did not overspend this allowance.
            if ($spent <= $amount) {
                $allowance['pct'] = round(($spent / $amount) * 100);
            }
        }

        return $allowance;
    }

}