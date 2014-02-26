<?php

use Carbon\Carbon as Carbon;

/**
 * Class ReportHelper
 */
class ReportHelper
{
    /**
     * Get some basic information for a year.
     *
     * @param Carbon $date The year
     *
     * @return array
     */
    public static function basicInformation(Carbon $date)
    {
        $data = [];
        $income = floatval(
            Auth::user()->transactions()->incomes()->inYear($date)->sum(
                'amount'
            )
        );
        $expenses = floatval(
            Auth::user()->transactions()->expenses()->inYear($date)->sum(
                'amount'
            )
        );
        $data['totalEarned'] = $income;
        $data['totalSpent'] = $expenses;
        $data['totalDiff'] = $expenses + $income;

        return $data;

    }

    /**
     * Get some basic account information for the year
     *
     * @param Carbon $date The year.
     *
     * @return array
     */
    public static function accountInformation(Carbon $start, Carbon $end)
    {

        $accounts = [];
        $accounts['accounts'] = Auth::user()->accounts()->get();
        $startNw = 0;
        $endNw = 0;
        foreach ($accounts['accounts'] as $account) {
            $startNw += $account->balanceOnDate($start);
            $endNw += $account->balanceOnDate($end);
        }
        $diffNw = $endNw - $startNw;
        $accounts['netWorthStart'] = $startNw;
        $accounts['netWorthEnd'] = $endNw;
        $accounts['netWorthDifference'] = $diffNw;

        return $accounts;
    }

    /**
     * Get some basic object information (components)
     *
     * @param Carbon $date     The year
     * @param string $type     The type
     * @param int    $sortFlag SORT_ASC|SORT_DESC
     *
     * @return array
     */
    public static function objectInformation(
        Carbon $date, $type, $sortFlag
    ) {

        $objects = Auth::user()->components()->where('type', $type)->get();
        $rawData = [];
        $amount = [];
        foreach ($objects as $object) {
            $query = $object->transactions()->inYear($date);
            if ($sortFlag == SORT_ASC) {
                $query->expenses();
            } else {
                $query->incomes();
            }
            $sum = floatval(
                $query->sum(
                    'amount'
                )
            );
            if ($sum != 0.0) {

                $rawData[] = ['id'  => $object->id, 'name' => $object->name,
                              'sum' => $sum];
            }
        }

        foreach ($rawData as $key => $row) {
            $amount[$key] = $row['sum'];
        }

        // sort by amount:
        array_multisort($amount, $sortFlag, $rawData);
        $finalData = [];
        foreach ($rawData as $index => $obj) {
            if ($index > 10) {
                break;
            }
            $finalData[] = $obj;
        }

        return $finalData;

    }

    public static function allowanceDetails(Carbon $start)
    {
        $start->startOfYear();
        $end = clone $start;
        $end = $end->endOfYear();
        $current = clone $start;
        $result = [];

        while ($current <= $end) {
            $entry['date'] = $current->format('F Y');
            $entry['inside'] = [];
            $entry['outside'] = [];

            $entry['inside'] = Auth::user()->transactions()->expenses()
                ->inMonth($current)->where('ignoreallowance', 0)->get();
            $entry['inside_sum'] = Auth::user()->transactions()->expenses()
                ->inMonth($current)->where('ignoreallowance', 0)->sum('amount');


            $entry['outside'] = Auth::user()->transactions()->expenses()
                ->inMonth($current)->where('ignoreallowance', 1)->get();
            $entry['outside_sum'] = Auth::user()->transactions()->expenses()
                ->inMonth($current)->where('ignoreallowance', 1)->sum('amount');

            if (floatval($entry['outside_sum']) != 0) {
                $result[] = $entry;
            }
            $current->addMonth();
        }

        return $result;

    }
}