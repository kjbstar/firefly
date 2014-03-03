<?php

class ReportHelper
{

    /**
     * @param $type
     * @param $predictables
     */
    public static function ieList($type, $predictables)
    {
        $query = Auth::user()->transactions()->groupBy('month');
        switch ($type) {
            case 'incomes':
                $query->incomes();
                break;
            case 'expenses':
                $query->expenses();
                break;
        }
        if ($predictables === true) {
            $query->whereNotNull('predictable_id');
        } else {
            $query->whereNull('predictable_id');
        }
        $result = $query->get(
            [DB::Raw('DATE_FORMAT(date,"%m-%Y") as `month`'),
             DB::Raw('SUM(`amount`) as `total`')]
        );
        $list = [];
        foreach($result as $row) {
            switch($type) {
                case 'incomes':
                    $list[$row->month] = $row->total;
                    break;
                case 'expenses':
                    $list[$row->month] = $row->total*-1;
                    break;
            }
        }
        return $list;
    }
    /**
     * // query + array for all predictable expenses:
     * $result = Auth::user()->transactions()->groupBy('month')->expenses()
     * ->get(
     * [DB::Raw('DATE_FORMAT(date,"%m-%Y") as `month`'),
     * DB::Raw('SUM(`amount`) as `total`')]
     * );
     * $expenses = [];
     * foreach ($result as $row) {
     * $expenses[$row->month] = floatval($row->total) * -1;
     * }
     * unset($result);
     *
     * // same for all incomes:
     * $result = Auth::user()->transactions()->groupBy('month')->incomes()
     * ->get(
     * [DB::Raw('DATE_FORMAT(date,"%m-%Y") as `month`'),
     * DB::Raw('SUM(`amount`) as `total`')]
     * );
     * $incomes = [];
     * foreach ($result as $row) {
     * $incomes[$row->month] = floatval($row->total);
     * }
     * unset($result);
     */
}