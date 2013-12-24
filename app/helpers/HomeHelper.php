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
        $query = Auth::user()->accounts()->notHidden()->get();
        $accounts = [];

        foreach ($query as $account) {
            $url = URL::Route(
                'accountoverview',
                [$account->id, $date->format('Y'), $date->format('m')]
            );

            $entry = [];
            $entry['name'] = $account->name;
            $entry['url'] = $url;
            $entry['balance'] = $account->balanceOnDate($start);
            $entry['current'] = $account->balanceOnDate($date);
            $entry['diff'] = $entry['current'] - $entry['current'];
            $accounts[] = $entry;
        }
        unset($query, $entry);

        return $accounts;
    }

    /**
     * Returns a list of transactions for the home page for a given month.
     *
     * @param Carbon $date
     *
     * @return array
     */
    public static function homeTransactionList(Carbon $date)
    {
        return Auth::user()->transactions()->orderBy(
            'date', 'DESC'
        )->inMonth($date)->orderBy('id', 'DESC')->take(5)->get();

    }

    /**
     * Returns a list of transfers for the home page.
     *
     * @param Carbon $date
     *
     * @return array
     */
    public static function homeTransferList(Carbon $date)
    {
        return Auth::user()->transfers()->orderBy('date', 'DESC')->inMonth(
            $date
        )->orderBy('id', 'ASC')->take(10)->get();

    }

    /**
     * Shows a chart for a beneficiary, category or budget.
     *
     * @param string $type
     * @param int    $year
     * @param int    $month
     */
    public static function homeComponentChart($type, $year, $month)
    {
        $date = Toolkit::parseDate($year, $month);
        if (is_null($date)) {
            $date = new Carbon;
        }

        $objects = self::homeComponentList(Str::singular($type), $date);
        $chart = App::make('gchart');
        $chart->addColumn(ucfirst($type), 'string');
        $chart->addColumn('Budgeted', 'number', 'old-data');
        $chart->addColumn('Amount', 'number');


        foreach ($objects as $index => $data) {
            // fix the amount:
            $amount
                = $data['amount'] < 0 ? $data['amount'] * -1 : $data['amount'];
            $max = $data['limit'] ?$data['limit'] : $amount;
            if ($index < 10) {
                $chart->addRow(['f' => $data['name'], 'v' => $data['id']],
                    $max,$amount);
            }
        }
        $chart->generate();
        $return = $chart->getData();

        /*
         * Now that the chart data is there, we can find out if these
         * components have limits and supplement the information with
         * "budgetary" information, displaying the limit we've set for these
         * components (if any).
         *
         * Remember that limitData is a chart in itself and can be displayed
         * seperately.
         */

//        $return['limitData'] = self::homeComponentChartLimitData(
//            $limitedObjects, $date
//        );

        return $return;

    }

    /**
     * Returns a list of [type]s for the home page for a given date (month).
     *
     * @param string $type
     * @param Carbon $date
     *
     * @return array
     */
    public static function homeComponentList($type, Carbon $date)
    {
        $objects = [];
        // a special empty component:
        $empty = ['id' => 0, 'name' => '(No ' . $type . ')', 'amount' => 0,
                  'url' => '#', 'limit' => null];


        $limits = [];
        // get all transactions for this month.
        // later on, we filter on the component.
        $transactions = Auth::user()->transactions()->with('components')
            ->expenses()->inMonth($date)->get();

        foreach ($transactions as $t) {
            $component = $t->getComponentByType($type);
            if (is_null($component)) {
                $empty['amount'] += $t->amount;
                continue;
            }
            $id = intval($component->id);
            if (isset($objects[$id])) {
                // append data:
                $objects[$id]['amount'] += floatval($t->amount);
            } else {
                // new object:
                $url = URL::Route(
                    $type . 'overview',
                    [$component->id, $date->format('Y'), $date->format('m')]
                );
                $current = [];
                $current['id'] = $component->id;
                $current['name'] = $component->name;
                $current['amount'] = floatval($t->amount);
                $current['url'] = $url;
                $current['limit'] = null;
                $current['left'] = 100;
                $objects[$id] = $current;
                // find a limit for this month
                // and save it to $limits
                $limit = $component->limits()->inMonth($date)->first();
                if ($limit) {
                    $limits[$id] = $limit;
                }
            }
            unset($current);
        }
        $objects[] = $empty;

        // loop the $limits array and check the $objects:
        foreach ($limits as $id => $limit) {
            $object = $objects[$id];
            $spent = $object['amount'] * -1;
            $max = floatval($limit->amount);
            $object['limit'] = $max;
            if ($spent > $max) {
                $object['overpct'] = round(
                    ($max / $spent) * 100
                );
                $object['spent'] = 100 - $object['overpct'];
            } else {
                $object['spent'] = round(
                    ($spent / $max) * 100
                );
                $object['left'] = 100 - $object['spent'];
            }
            $objects[$id] = $object;
        }
        $amount = [];
        foreach ($objects as $key => $row) {
            $amount[$key] = $row['amount'];
        }

        array_multisort($amount, SORT_ASC, $objects);

        return $objects;
    }

    /**
     * For each entry in the data array, find the component
     * and possibly a limit that goes with it. The sum of the
     * returned list should be 2000, which currently is a hard coded value.
     *
     * @param $data
     */
    public static function homeComponentChartLimitData($data, Carbon $date)
    {

        // first force this to be just 2000 euro's.
        $chart = App::make('gchart');
        $chart->addColumn('Some object', 'string');
        $chart->addColumn('Amount', 'number');
        $left = 2000;
        foreach ($data as $name => $row) {
            $amount = $row['amount'] < 0 ? $row['amount'] * -1 : $row['amount'];
            //echo 'now at '.$row['id'].'<br>';

            $limit = Limit::where(
                'component_id', $row['id']
            )->inMonth($date)->first();
            $left -= floatval($amount);
            if (!is_null($limit)) {
                $amount = floatval($limit->amount);
            }

            $chart->addRow(['v' => $row['id'], 'f' => $name], $amount);
        }
        $left = $left < 0 ? 0 : $left;
        $chart->addRow('Left', $left);


        $chart->generate();

        return $chart->getData();
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
        // some dates:
        $realDay = new Carbon;

        $date = Toolkit::parseDate($year, $month);
        $date->endOfMonth();
        $start = clone $date;
        $start->startOfMonth();

        $chart = App::make('gchart');
        $chart->addColumn('Day of the month', 'date');

        // array holds balances.
        $balances = [];

        // add accounts and set initial balance.
        $accounts = Auth::user()->accounts()->notHidden()->get();
        foreach ($accounts as $index => $account) {
            $chart->addColumn($account->name, 'number');
            $chart->addCertainty(($index + 1));
            $balances[$account->id] = 0;
        }
        $row = 0;
        while ($start <= $date) {
            $current = clone $start;

            $chart->addCell($row, 0, $current);

            $cell = 1;
            foreach ($accounts as $account) {
                // first row? set the balance from the calculation,
                // prediction be damned!
                if ($row === 0) {
                    $balances[$account->id] = $account->balanceOnDate($current);
                    $certainty = true;
                }

                // if were past the first row, we dare predict the balance:
                if ($row > 0 && $current > $realDay) {
                    $prediction = $account->predictOnDate($current);
                    $balances[$account->id] -= $prediction['prediction'];
                    $certainty = false;
                }
                // if we're past the first row we might not HAVE to predict.
                if ($row > 0 && $current <= $realDay) {
                    $balances[$account->id] = $account->balanceOnDate($current);
                    $certainty = true;
                }
                // once we have all this, we set some data:
                $chart->addCell($row, $cell, $balances[$account->id]);
                $cell++;

                $chart->addCell($row, $cell, $certainty);
                $cell++;
            }
            $row++;
            $start->addDay();
        }

        $chart->generate();

        return $chart->getData();
    }
} 