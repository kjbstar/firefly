<?php

/** @noinspection PhpIncludeInspection */
require_once(app_path() . '/helpers/Toolkit.php');
/** @noinspection PhpIncludeInspection */
include_once(app_path() . '/helpers/ReportHelper.php');

use Carbon\Carbon as Carbon;

/**
 * Class ReportController
 */
class ReportController extends BaseController
{
    /**
     * Index for report controller
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $first = Toolkit::getEarliestEvent();
        $today = new Carbon;
        $first->startOfYear();
        $years = [];
        while ($first <= $today) {
            $year = $first->format('Y');
            $years[$year] = [
                '01' => 'January',
                '02' => 'February',
                '03' => 'March',
                '04' => 'April',
                '05' => 'May',
                '06' => 'June',
                '07' => 'July',
                '08' => 'August',
                '09' => 'September',
                '10' => 'October',
                '11' => 'November',
                '12' => 'December'
            ];
            $first->addYear();
        }


        return View::make('reports.index')->with('title', 'Reports')->with(
            'years', $years
        );
    }

    /**
     * Report for a month.
     *
     * @param $year
     * @param $month
     *
     * @return \Illuminate\View\View
     */
    public function month($year, $month)
    {
        $date = Toolkit::parseDate($year, $month);
        $title = 'Report for ' . $date->format('F Y');

        $summary = ReportHelper::summary($date, 'month');
        $biggest = ReportHelper::biggestExpenses($date, 'month');
        $predicted = ReportHelper::predicted($date);
        $incomes = ReportHelper::incomes($date, 'month');

        $expenses = [
            'category'    => ReportHelper::expensesGrouped($date, 'month', 'category'),
            'budget'      => ReportHelper::expensesGrouped($date, 'month', 'budget'),
            'beneficiary' => ReportHelper::expensesGrouped($date, 'month', 'beneficiary'),
        ];

        return View::make('reports.month')->with('date', $date)->with('title', $title)->with('summary', $summary)->with(
            'biggest', $biggest
        )->with('predicted', $predicted)->with('expenses', $expenses)->with(
                'incomes', $incomes
            );
    }

    public function monthPieChart($year, $month, $type)
    {
        // get data:
        $date = Toolkit::parseDate($year, $month);
        $array = ['beneficiary', 'budget', 'category'];
        if (in_array($type, $array)) {
            $data = ReportHelper::expensesGrouped($date, 'month', $type);
        } else {
            App::abort(404);
        }

        // generate chart:
        $chart = App::make('gchart');
        $chart->addColumn(ucfirst($type) . ' name', 'string');
        $chart->addColumn('Amount in ' . $date->format('F Y'), 'number');

        // let's see if we can group stuff smaller than
        // two percent?
        $sum = 0;


        foreach ($data as $row) {
            $chart->addRow($row['component']['name'], $row['component']['sum'] * -1);
            $sum += ($row['component']['sum'] * -1);
        }

        $chart->generate();
        return Response::json($chart->getData());

    }

    /**
     * Year report.
     *
     * @param $year
     *
     * @return \Illuminate\View\View
     */
    public function year($year)
    {
        $date = Toolkit::parseDate($year, 1);
        $title = 'Report for ' . $date->format('Y');

        $summary = ReportHelper::summary($date, 'year');
        $biggest = ReportHelper::biggestExpenses($date, 'year');
        $incomes = ReportHelper::incomes($date, 'year');
        $months = ReportHelper::months($date);

        return View::make('reports.year')->with('date', $date)->with('title', $title)->with('summary', $summary)->with(
            'biggest', $biggest
        )->with(
                'incomes', $incomes
            )->with('months', $months);

    }

    /**
     * Generates a chart for the accounts in that month and their balance.
     *
     * @param $year
     * @param $month
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function monthAccounts($year, $month)
    {
        // cache!
        $key = Auth::user()->id . '-month-account-' . $year . $month . 'aac';
        if (Cache::has($key)) {
            // @codeCoverageIgnoreStart
            return Response::json(Cache::get($key));
            // @codeCoverageIgnoreEnd
        }

        // dates!
        $start = Toolkit::parseDate($year, $month);
        $end = clone $start;
        $end->endOfMonth();
        $now = new Carbon;
        $current = clone $start;

        // all relevant accounts!
        $accounts = Auth::user()->accounts()->notHidden()->notShared()->where(
            'openingbalancedate', '<', $end->format('Y-m-d')
        )->get();

        // create a chart!
        $chart = App::make('gchart');
        $chart->addColumn('Day', 'date');
        foreach ($accounts as $account) {
            $chart->addColumn($account->name, 'number');
        }


        while ($current <= $end) {
            $row = [clone $current];

            foreach ($accounts as $account) {
                if ($current <= $now) {
                    $row[] = $account->balanceOnDate($current);
                } else {
                    $row[] = null;
                }
            }
            $chart->addRowArray($row);
            $current->addDay();
        }

        $chart->generate();
        $data = $chart->getData();
        Cache::put($key, $data, 1440);
        return Response::json($data);
    }

    /**
     * Generates a chart of that year and the balances of the accounts
     * in that year.
     *
     * @param $year
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function yearAccounts($year)
    {
        // cache!
        $key = Auth::user()->id . '-year-account-' . $year . 'aac';
        if (Cache::has($key)) {
            // @codeCoverageIgnoreStart
            return Response::json(Cache::get($key));
            // @codeCoverageIgnoreEnd
        }

        // dates!
        $start = Toolkit::parseDate($year, 1);
        $start->startOfYear();
        $end = clone $start;
        $end->endOfYear();
        $now = new Carbon;
        $current = clone $start;

        // all relevant accounts!
        $accounts = Auth::user()->accounts()->notHidden()->notShared()->where(
            'openingbalancedate', '<', $end->format('Y-m-d')
        )->get();

        // create a chart!
        $chart = App::make('gchart');
        $chart->addColumn('Month', 'date');
        foreach ($accounts as $account) {
            $chart->addColumn($account->name, 'number');
        }


        while ($current <= $end) {
            $row = [clone $current];

            foreach ($accounts as $account) {
                if ($current <= $now) {
                    $row[] = $account->balanceOnDate($current);
                } else {
                    $row[] = null;
                }
            }
            $chart->addRowArray($row);
            $current->addMonth();
        }

        $chart->generate();
        $data = $chart->getData();
        Cache::put($key, $data, 1440);
        return Response::json($data);
    }


}