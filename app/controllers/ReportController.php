<?php
use Carbon\Carbon as Carbon;

require_once(app_path() . '/helpers/ReportHelper.php');


/**
 * Class ReportController
 */
class ReportController extends BaseController
{

    /**
     * Show the index for the reports.
     *
     * @return \Illuminate\View\View
     */
    public function showIndex()
    {
        $first = Auth::user()->accounts()->orderBy(
            'openingbalancedate', 'ASC'
        )->first()->openingbalancedate;
        $now = new Carbon;
        $years = [];
        while ($first < $now) {
            $current = clone $first;
            $years[] = $current->format('Y');
            $first->addYear();
        }

        return View::make('reports.index')->with('years', $years);
    }

    /**
     * Shows the report for a year
     *
     * @param int $year The year.
     *
     * @return \Illuminate\View\View
     */
    public function showYearlyReport($year)
    {
        $start = new Carbon($year . '-01-01');
        $start->startOfYear();
        $end = clone $start;
        $end->endOfYear();

        // basic information:
        $data = ReportHelper::basicInformation($start);
        // account information:
        $accounts = ReportHelper::accountInformation($start);

        $benefactors = ReportHelper::objectInformation(
            $start, 'beneficiary', SORT_DESC
        );
        $fans = ReportHelper::objectInformation(
            $start, 'beneficiary', SORT_ASC
        );
        $spentMostCategories = ReportHelper::objectInformation(
            $start, 'category', SORT_ASC
        );

        return View::make('reports.year')->with('date', $start)->with(
            'data', $data
        )->with('accounts', $accounts)->with('end', $end)->with(
                'benefactors', $benefactors
            )->with(
                'fans', $fans
            )->with('spentMostCategories', $spentMostCategories)->with(
                'title', 'Report for ' . $year
            );
    }

    /**
     * Generates the chart with income / expenses and your net worth.
     *
     * @param $year
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function netWorthChart($year)
    {
        $start = new Carbon($year . '-01-01');
        $start->startOfYear();
        $end = clone $start;
        $end->endOfYear();

        $chart = App::make('gchart');
        $chart->addColumn('Month', 'date');
        $chart->addColumn('Income', 'number');
        $chart->addColumn('Expenses', 'number');
        $chart->addColumn('Net worth', 'number');
        $accounts = Auth::user()->accounts()->get();

        while ($start < $end) {
            $current = clone $start;
            $current->endOfMonth();

            $income = floatval(
                Auth::user()->transactions()->incomes()->inMonth($current)->sum(
                    'amount'
                )
            );
            $expenses = floatval(
                Auth::user()->transactions()->expenses()->inMonth($current)
                    ->sum('amount')
            );
            $expenses = $expenses * -1;
            $netWorth = 0;

            // net worth:
            foreach ($accounts as $a) {
                $netWorth += $a->balanceOnDate($current);
            }


            $chart->addRow($current, $income, $expenses, $netWorth);

            $start->addMonth();
        }

        $chart->generate();

        return Response::json($chart->getData());
    }

    /**
     * Generates a pie chart of your top 10 components ($type) ordered by
     * $sort, in the year $year.
     *
     * @param int    $year The year
     * @param string $type The component
     * @param string $sort asc|desc
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function objectChart($year, $type, $sort)
    {
        $sortFlag = $sort == 'asc' ? SORT_ASC : SORT_DESC;
        $date = new Carbon($year . '-01-01');
        $data = ReportHelper::objectInformation($date, $type, $sortFlag);

        // make chart
        $chart = App::make('gchart');
        $chart->addColumn('Object', 'string');
        $chart->addColumn('Amount', 'number');

        foreach ($data as $entry) {
            $amount = $entry['sum'] < 0 ? $entry['sum'] * -1 : $entry['sum'];
            $chart->addRow(
                ['v' => $entry['id'], 'f' => $entry['name']], $amount
            );
        }

        $chart->generate();

        return Response::json($chart->getData());


    }
}