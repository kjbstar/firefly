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
        // make a chart:
        $chart = App::make('gchart');
        $chart->addColumn(ucfirst($type), 'string');
        $chart->addColumn('Amount', 'number');
        foreach ($objects as $name => $data) {
            $amount
                = $data['amount'] < 0 ? $data['amount'] * -1 : $data['amount'];
            $chart->addRow(['f' => $name, 'v' => $data['id']], $amount);
        }
        $chart->generate();

        return $chart->getData();

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
        $empty = ['id'  => 0, 'name' => '(no ' . $type . ')', 'amount' => 0,
                  'url' => '#', 'limit' => null];

        $limits = [];
        // get all transactions for this month that have this component.
        $transactions = Auth::user()->transactions()->hasComponent($type)
            ->inMonth($date)->get();

        foreach ($transactions as $t) {
            $component = $t->components->first();
            if (!$component) {
                // TOOD add to "no object"!
                $empty['amount'] += $t->amount;
                continue;
            }
            $name = $component->name;
            $componentType = $component->type;
            if (isset($objects[$name]) && $componentType == $type) {
                // append data:
                $current = $objects[$name];
                $current['amount'] += floatval($t->amount);
                $objects[$name] = $current;

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
                $objects[$name] = $current;
                // find a limit for this month
                // and save it to $limits
                $limit = $component->limits()->inMonth($date)->first();
                if ($limit) {
                    $limits[$name] = $limit;
                }
            }
            unset($current);
        }
        $objects['(no ' . $type . ')'] = $empty;
        // loop the $limits array and check the $objects:
        foreach ($limits as $name => $limit) {
            $object = $objects[$name];
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
            $objects[$name] = $object;
        }
        // TODO loop again and cut off the "left-overs".
        $amount = [];
        foreach ($objects as $key => $row) {
            $amount[$key] = $row['amount'];
        }

        array_multisort($amount, SORT_ASC, $objects);


        return $objects;
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