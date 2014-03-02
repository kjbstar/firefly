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
        return View::make('reports.year')->with('title', 'Report for ' . $year)
            ->with('year', $year);
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

        while ($start <= $end) {
            $income = floatval(
                Auth::user()->transactions()->incomes()->inMonth($start)->sum(
                    'amount'
                )
            );
            $expenses = floatval(
                    Auth::user()->transactions()->expenses()->inMonth($start)
                        ->sum('amount')
                ) * -1;
            $chart->addRow(clone $start, $income, $expenses);
            $start->addMonth();
        }

        $chart->generate();

        return Response::json($chart->getData());
    }
}