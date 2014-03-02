<?php

/** @noinspection PhpIncludeInspection */
require_once(app_path() . '/helpers/Toolkit.php');

use Carbon\Carbon as Carbon;

class ReportController extends BaseController
{
    public function index()
    {
        $first = Toolkit::getEarliestEvent();
        $today = new Carbon;
        $first->startOfYear();
        $years = [];
        while ($first <= $today) {
            $years[] = $first->format('Y');
            $first->addYear();
        }


        return View::make('reports.index')->with('title', 'Reports')->with(
            'years', $years
        );
    }

    public function year($year)
    {
        // get net worth at start of year.
        $start = new Carbon($year . '-01-01');
        $end = new Carbon($year . '-12-31');
        $startNetWorth = 0;
        $endNetWorth = 0;
        foreach (Auth::user()->accounts()->get() as $account) {
            $startNetWorth += $account->balanceOnDate($start);
            $endNetWorth += $account->balanceOnDate($end);
        }

        // get the 10 biggest expenses:
        $expenses = Auth::user()->transactions()->inYear($start)->expenses()
            ->orderBy('amount', 'ASC')->take(5)->get();

        // get the 10 biggest fans
        $result = Auth::user()->components()
            ->leftJoin('component_transaction','component_transaction.component_id','=','components.id')
            ->leftJoin('transactions','component_transaction.transaction_id','=','transactions.id')
            ->where('components.type','beneficiary')
            ->where(DB::Raw('DATE_FORMAT(transactions.date,"%Y")'),$year)
            ->groupBy('components.id')
            ->orderBy('total')
            ->take(5)
            ->get(['components.name',DB::Raw('SUM(`transactions`.`amount`) as `total`')]);

        // total income, total expenses
        $totalIncome = Auth::user()->transactions()->incomes()->inYear($start)->sum('amount');
        $totalExpenses = Auth::user()->transactions()->expenses()->inYear($start)->sum('amount');


        return View::make('reports.year')->with('title', 'Report for ' . $year)
            ->with('year', $year)->with('startNetWorth', $startNetWorth)->with(
                'endNetWorth', $endNetWorth
            )->with('expenses', $expenses)->with('fans', $result)->with('totalIncome',$totalIncome)->with('totalExpenses',$totalExpenses);
    }

    public function yearIeChart($year)
    {
        // dates
        $start = new Carbon($year . '-01-01');
        $end = clone $start;
        $end->endOfYear();

        // chart:
        $chart = App::make('gchart');
        $chart->addColumn('Month', 'date');
        $chart->addColumn('Income', 'number');
        $chart->addColumn('Expenses', 'number');

        // query + array for all expenses:
        $result = Auth::user()->transactions()->groupBy('month')->expenses()->get([DB::Raw('DATE_FORMAT(date,"%m-%Y") as `month`'),DB::Raw('SUM(`amount`) as `total`')]);
        $expenses = [];
        foreach($result as $row) {
            $expenses[$row->month] = floatval($row->total)*-1;
        }
        unset($result);

        // same for all incomes:
        $result = Auth::user()->transactions()->groupBy('month')->incomes()->get([DB::Raw('DATE_FORMAT(date,"%m-%Y") as `month`'),DB::Raw('SUM(`amount`) as `total`')]);
        $incomes = [];
        foreach($result as $row) {
            $incomes[$row->month] = floatval($row->total);
        }
        unset($result);


        while ($start <= $end) {
            $date = $start->format('m-Y');
            $income = isset($incomes[$date]) ? $incomes[$date] : 0;
            $expense = isset($expenses[$date]) ? $expenses[$date] : 0;

            $chart->addRow(clone $start, $income, $expense);
            $start->addMonth();
        }

        $chart->generate();
        return Response::json($chart->getData());
    }
}