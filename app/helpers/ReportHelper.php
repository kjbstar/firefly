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
    public static function accountInformation(Carbon $start,Carbon $end)
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
            if($sortFlag == SORT_ASC) {
                $query->expenses();
            } else {
                $query->incomes();
            }
            $sum = floatval(
                $query->sum(
                    'amount'
                )
            );
            if($sum != 0.0) {

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
}