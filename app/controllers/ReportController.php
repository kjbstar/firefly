<?php

/** @noinspection PhpIncludeInspection */
require_once(app_path() . '/helpers/Toolkit.php');
/** @noinspection PhpIncludeInspection */
include_once(app_path() . '/helpers/ReportHelper.php');

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

        // get the X biggest expenses:
        $expenses = Auth::user()->transactions()->inYear($start)->expenses()
            ->orderBy('amount', 'ASC')->whereNull('predictable_id')->take(10)
            ->get();

        // get the X biggest fans
        $result = Auth::user()->components()->leftJoin(
            'component_transaction', 'component_transaction.component_id', '=',
            'components.id'
        )->leftJoin(
                'transactions', 'component_transaction.transaction_id', '=',
                'transactions.id'
            )->whereNull('transactions.predictable_id')->where(
                'components.type', 'beneficiary'
            )->where(
                DB::Raw('DATE_FORMAT(transactions.date,"%Y")'), $year
            )->groupBy('components.id')->orderBy('total')->take(10)->get(
                ['components.name',
                 DB::Raw('SUM(`transactions`.`amount`) as `total`')]
            );

        // total income, total expenses
        $totalIncome = Auth::user()->transactions()->incomes()->inYear($start)
            ->sum('amount');
        $totalExpenses = Auth::user()->transactions()->expenses()->inYear(
            $start
        )->sum('amount');


        return View::make('reports.year')->with('title', 'Report for ' . $year)
            ->with('year', $year)->with('startNetWorth', $startNetWorth)->with(
                'endNetWorth', $endNetWorth
            )->with('expenses', $expenses)->with('fans', $result)->with(
                'totalIncome', $totalIncome
            )->with('totalExpenses', $totalExpenses);
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
        $chart->addColumn('Expenses (predictable)', 'number');
        $chart->addColumn('Expenses (not predictable)', 'number');

        // all data sets:
        $inPredList = ReportHelper::ieList('incomes', null);
        $outPredList = ReportHelper::ieList('expenses', true);
        $outNoPredList = ReportHelper::ieList('expenses', false);


        while ($start <= $end) {
            $date = $start->format('m-Y');
            $income = isset($inPredList[$date]) ? $inPredList[$date] : 0;

            $outPred = isset($outPredList[$date]) ? $outPredList[$date] : 0;
            $outNoPred = isset($outNoPredList[$date]) ? $outNoPredList[$date]
                : 0;

            $chart->addRow(clone $start, $income, $outPred, $outNoPred);


            $start->addMonth();
        }

        $chart->generate();

        return Response::json($chart->getData());
    }
}