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
            $year = $first->format('Y');
            $years[$year] = ['01' => 'January', '02' => 'February',
                             '03' => 'March', '04' => 'April', '05' => 'May',
                             '06' => 'June', '07' => 'July', '08' => 'August',
                             '09' => 'September', '10' => 'October',
                             '11' => 'November', '12' => 'December',];
            $first->addYear();
        }


        return View::make('reports.index')->with('title', 'Reports')->with(
            'years', $years
        );
    }

    public function month($year, $month)
    {
        $start = new Carbon($year . '-' . $month . '-01');
        $end = clone $start;
        $end->endOfMonth();

        // transactions
        $predicted = Auth::user()->transactions()->expenses()->inMonth($start)
            ->whereNotNull('predictable_id')->orderBy('date', 'ASC')->get();
        $predictedSum = Auth::user()->transactions()->inMonth($start)
            ->whereNotNull('predictable_id')->expenses()->orderBy('date', 'ASC')
            ->sum('amount');
        $notPredicted = Auth::user()->transactions()->expenses()->orderBy(
            'date', 'ASC'
        )->inMonth($start)->whereNull('predictable_id')->get();
        $notPredictedSum = Auth::user()->transactions()->inMonth($start)
            ->whereNull('predictable_id')->expenses()->orderBy('date', 'ASC')
            ->sum('amount');

        // sums:
        $sumOut = floatval($notPredictedSum + $predictedSum);
        $sumIn
            = floatval(
            Auth::user()->transactions()->inMonth($start)->orderBy(
                'date', 'ASC'
            )->incomes()->sum('amount')
        );

        $sums = ['sumIn' => $sumIn, 'sumOut' => $sumOut];

        // net worth:
        $startNetWorth = 0;
        $endNetWorth = 0;
        foreach (Auth::user()->accounts()->get() as $a) {
            $startNetWorth += $a->balanceOnDate($start);
            $endNetWorth += $a->balanceOnDate($end);
        }
        $netWorth = ['start' => $startNetWorth, 'end' => $endNetWorth];

        // incomes:
        $incomes = [];
        $incomes['transactions'] = Auth::user()->transactions()->incomes()
            ->orderBy(
                'date', 'ASC'
            )->inMonth($start)->whereNull('predictable_id')->get();
        $incomes['sum'] = Auth::user()->transactions()->incomes()->orderBy(
            'date', 'ASC'
        )->inMonth($start)->whereNull('predictable_id')->sum('amount');


        $transactions = ['predicted'       => $predicted,
                         'predictedSum'    => $predictedSum,
                         'notPredicted'    => $notPredicted,
                         'notPredictedSum' => $notPredictedSum];

        // components:
        $components = Auth::user()->components()->reporting()->orderBy(
            'type', 'DESC'
        )->get();
        foreach ($components as $c) {
            $c->sum = $c->transactions()->inMonth($start)->sum('amount');
        }


        return View::make('reports.month')->with(
            'title', 'Report for ' . $start->format('F Y')
        )->with('start', $start)->with('transactions', $transactions)->with(
                'sums', $sums
            )->with('netWorth', $netWorth)->with('end', $end)->with(
                'incomes', $incomes
            )->with('components', $components);
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

        // count the components for a chart:
        $components = Auth::user()->components()->reporting()->count();

        // buttons:
        $current = clone $start;
        $buttons = [];
        while ($current <= $end) {
            $buttons[] = ['month' => $current->format('m'),
                          'year'  => $current->format('Y'),
                          'date'  => $current->format('F Y')];
            $current->addMonth();
        }


        return View::make('reports.year')->with('title', 'Report for ' . $year)
            ->with('year', $year)->with('startNetWorth', $startNetWorth)->with(
                'endNetWorth', $endNetWorth
            )->with('expenses', $expenses)->with('fans', $result)->with(
                'totalIncome', $totalIncome
            )->with('totalExpenses', $totalExpenses)->with('buttons', $buttons)
            ->with('components', $components);
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

    public function yearCompare($yearOne, $yearTwo)
    {
        $one = new Carbon($yearOne . '-01-01');
        $two = new Carbon($yearTwo . '-01-01');
        if ($one->eq($two)) {
            return App::abort(500);
        }

        return View::make('reports.compare-year')->with(
            'title',
            'Comparing ' . $one->format('Y') . ' with ' . $two->format('Y')
        )->with('one', $one)->with('two', $two);
    }

    public function monthCompare($yearOne, $monthOne, $yearTwo, $monthTwo)
    {
        $one = Toolkit::parseDate($yearOne, $monthOne);
        $two = Toolkit::parseDate($yearTwo, $monthTwo);
        if ($one->eq($two)) {
            return App::abort(500);
        }
        $numbers = [];
        // incomes + expenses
        foreach (['one' => $one, 'two' => $two] as $key => $date) {
            $numbers[$key]['in'] = Auth::user()->transactions()->incomes()
                ->orderBy(
                    'date', 'ASC'
                )->inMonth($date)->sum('amount');
            $numbers[$key]['out'] = Auth::user()->transactions()->expenses()
                ->orderBy(
                    'date', 'ASC'
                )->inMonth($date)->sum('amount');

        }
        // start and end net worths:
        foreach (['one' => $one, 'two' => $two] as $key => $start) {
            $accounts = Auth::user()->accounts()->get();
            $end = clone $start;
            $end->endOfMonth();
            $numbers[$key]['net_start'] = 0;
            $numbers[$key]['net_end'] = 0;

            foreach ($accounts as $account) {
                $numbers[$key]['net_start'] += $account->balanceOnDate($start);
                $numbers[$key]['net_end'] += $account->balanceOnDate($end);
            }
        }
        // predictables:
        $predictables = ['predictables' => [], 'sum_one' => 0, 'sum_two' => 0];
        foreach (Auth::user()->predictables()->get() as $p) {
            $entry = ['description' => $p->description, 'id' => $p->id,
                      'one'         => null, 'two' => null];
            // get entry for one
            $entryOne = $p->transactions()->inMonth($one)->first();
            if (!is_nulL($entryOne)) {
                $entry['one'] = $entryOne;
                $predictables['sum_one'] += $entryOne->amount;
            }

            // get entry for two
            $entryTwo = $p->transactions()->inMonth($two)->first();
            if (!is_nulL($entryTwo)) {
                $entry['two'] = $entryTwo;
                $predictables['sum_two'] += $entryTwo->amount;
            }
            $predictables['predictables'][] = $entry;
        }
        // incomes:
        // first get 'one'
        $incomes = ['one_sum' => 0, 'two_sum' => 0, 'incomes' => []];
        $incomesOne = Auth::user()->transactions()->incomes()->inMonth($one)
            ->get();
        foreach ($incomesOne as $i) {
            $incomes['incomes'][$i->description]['one'] = ['id'     => $i->id,
                                                           'amount' => $i->amount];
            $incomes['one_sum'] += $i->amount;
        }
        // then get two.
        $incomesTwo = Auth::user()->transactions()->incomes()->inMonth($two)
            ->get();
        foreach ($incomesTwo as $i) {
            $incomes['incomes'][$i->description]['two'] = ['id'     => $i->id,
                                                           'amount' => $i->amount];
            $incomes['two_sum'] += $i->amount;
        }

        // get the components and list them.
        $comp = Auth::user()->components()->reporting()->get();
        $components = [];
        foreach ($comp as $c) {
            $entry = ['component' => $c, 'one' => floatval(
                $c->transactions()->inMonth($one)->sum('amount')
            ), 'two'              => floatval(
                $c->transactions()->inMonth($two)->sum('amount')
            )];
            $components[] = $entry;
        }
        unset($comp);


        return View::make('reports.compare-month')->with(
            'title',
            'Comparing ' . $one->format('F Y') . ' with ' . $two->format('F Y')
        )->with('one', $one)->with('two', $two)->with('numbers', $numbers)
            ->with('predictables', $predictables)->with('incomes', $incomes)
            ->with('components', $components);
    }

    public function monthCompareAccountChart(
        $yearOne, $monthOne, $yearTwo, $monthTwo
    ) {
        $one = Toolkit::parseDate($yearOne, $monthOne);
        $two = Toolkit::parseDate($yearTwo, $monthTwo);
        if ($one->eq($two)) {
            return App::abort(500);
        }
        $realDay = new Carbon;


        $account = Toolkit::getFrontpageAccount();
        $chart = App::make('gchart');
        $chart->addColumn('Day of month', 'string');
        $chart->addColumn('Balance in ' . $one->format('F Y'), 'number');
        $chart->addColumn('Balance in ' . $two->format('F Y'), 'number');
        // length of month in days:
        $lom = intval($one->format('t'));
        for ($i = 1; $i <= $lom; $i++) {

            $balanceOne = ($one <= $realDay) ? $account->balanceOnDate($one)
                : null;
            $balanceTwo = ($two <= $realDay) ? $account->balanceOnDate($two)
                : null;
            $chart->addRow('Day #' . $i, $balanceOne, $balanceTwo);
            $one->addDay();
            $two->addDay();
        }
        $chart->generate();

        return Response::json($chart->getData());

    }

    public function yearComponentsChart($year)
    {
        $start = new Carbon($year . '-01-01');
        $end = new Carbon($year . '-12-31');

        $components = Auth::user()->components()->reporting()->get();

        $chart = App::make('gchart');
        $chart->addColumn('Month', 'date');
        foreach ($components as $c) {
            $chart->addColumn($c->name, 'number');
        }


        while ($start <= $end) {

            $row = [];
            $row[] = clone $start;
            foreach ($components as $c) {
                $row[] = floatval(
                    $c->transactions()->inMonth($start)->sum('amount')
                );
            }
            $chart->addRowArray($row);


            $start->addMonth();

        }

        $chart->generate();

        return Response::json($chart->getData());
    }

}