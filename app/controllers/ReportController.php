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
        $year = intval($year);
        // end is the current year.
        $start = new Carbon(($year-1).'-12-31');
        $end = new Carbon($year.'-12-31');




        // basic information:
        $data = ReportHelper::basicInformation($end);
        // account information:
        $accounts = ReportHelper::accountInformation($start,$end);

        $benefactors = ReportHelper::objectInformation(
            $end, 'beneficiary', SORT_DESC
        );
        $fans = ReportHelper::objectInformation(
            $end, 'beneficiary', SORT_ASC
        );
        $spentMostCategories = ReportHelper::objectInformation(
            $end, 'category', SORT_ASC
        );

        $budgets = Auth::user()->components()->where('type','budget')->get();

        return View::make('reports.year')->with('start',
            $start)->with('end',$end)->with(
            'data', $data
        )->with('accounts', $accounts)->with('end', $end)->with(
                'benefactors', $benefactors
            )->with(
                'fans', $fans
            )->with('spentMostCategories', $spentMostCategories)->with(
                'title', 'Report for ' . $year
            )->with('budgets',$budgets);
    }

    public function objectOverviewChart($year, Component $component) {
        $start = new Carbon($year . '-01-01');
        $end = clone $start;
        $end->endOfYear();
        $chart = App::make('gchart');
        $chart->addColumn('Month', 'date');
        $chart->addColumn('Amount spent', 'number');
        $chart->addColumn('Amount predicted', 'number');
        $chart->addColumn('Amount budgeted', 'number');

        while($start <= $end) {
            $current = clone $start;

            // get amount spent:
            $spent = floatval($component->transactions()->inMonth($current)
                    ->expenses()->sum('amount')
                *-1);
            // get the limit:
            $limit  = $component->limits()->where('date',
                $current->format('Y-m-d'))->first();
            if($limit) {
                $limited = floatval($limit->amount);
            } else {
                $limited = null;
            }
            $prediction = $component->predictForMonth($current);

            $chart->addRow($current,$spent,$prediction,$limited);

            $start->addMonth();
        }

        $chart->generate();
        return Response::json($chart->getData());

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