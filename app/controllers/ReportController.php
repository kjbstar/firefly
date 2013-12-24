<?php
use Carbon\Carbon as Carbon;

class ReportController extends BaseController
{


    public function showYearlyReport($year)
    {
        $start = new Carbon($year . '-01-01');
        $start->startOfYear();
        $end = clone $start;
        $end->endOfYear();

        // basic information:
        $data = $this->basicInformation($start);
        // account information:
        $accounts = $this->accountInformation($start);

        $benefactors = $this->objectInformation(
            $start, 'beneficiary', SORT_DESC
        );
        $fans = $this->objectInformation(
            $start, 'beneficiary', SORT_ASC
        );
        $spentMostCategories = $this->objectInformation(
            $start, 'category', SORT_ASC
        );

        return View::make('reports.year')->with('date', $start)->with(
            'data', $data
        )->with('accounts', $accounts)->with('end', $end)->with(
                'benefactors', $benefactors
            )->with(
                'fans', $fans
            )->with('spentMostCategories', $spentMostCategories)->with
            ('title','Report for '.$year);
    }

    public function netWorthChart($year) {
        $start = new Carbon($year . '-01-01');
        $start->startOfYear();
        $end = clone $start;
        $end->endOfYear();

        $chart = App::make('gchart');
        $chart->addColumn('Month','date');
        $chart->addColumn('Income','number');
        $chart->addColumn('Expenses','number');
        $chart->addColumn('Net worth','number');
        $accounts = Auth::user()->accounts()->get();

        while($start < $end) {
            $current = clone $start;
            $current->endOfMonth();

            $income = floatval(Auth::user()->transactions()->incomes()->inMonth
                    ($current)->sum('amount'));
            $expenses = floatval(Auth::user()->transactions()->expenses()
                    ->inMonth
                    ($current)->sum('amount'));
            $expenses = $expenses * -1;
            $netWorth = 0;

            // net worth:
            foreach($accounts as $a) {
                $netWorth += $a->balanceOnDate($current);
            }


            $chart->addRow($current,$income,$expenses,$netWorth);

            $start->addMonth();
        }

        $chart->generate();
        return Response::json($chart->getData());
    }

    private function basicInformation(Carbon $date)
    {
        $data = [];
        $income = floatval(
            Auth::user()->transactions()->incomes()->inYear($date)->sum
                    ('amount')
        );
        $expenses = floatval(
            Auth::user()->transactions()->expenses()->inYear($date)->sum('amount')
        );
        $data['totalEarned'] = $income;
        $data['totalSpent'] = $expenses;
        $data['totalDiff'] = $expenses + $income;

        return $data;

    }

    private function accountInformation(Carbon $date)
    {
        $end = clone $date;
        $end->endOfYear();
        $accounts = [];
        $accounts['accounts'] = Auth::user()->accounts()->get();
        $startNw = 0;
        $endNw = 0;
        foreach ($accounts['accounts'] as $account) {
            $startNw += $account->balanceOnDate($date);
            $endNw += $account->balanceOnDate($end);
        }
        $diffNw = $endNw - $startNw;
        $accounts['netWorthStart'] = $startNw;
        $accounts['netWorthEnd'] = $endNw;
        $accounts['netWorthDifference'] = $diffNw;

        return $accounts;
    }

    private function objectInformation(
        Carbon $date, $type, $sortFlag
    ) {

        $objects = Auth::user()->components()->where('type', $type)->get();
        $rawData = [];
        $amount = [];
        foreach ($objects as $object) {
            $rawData[] = ['id'  => $object->id, 'name' => $object->name,
                          'sum' => floatval(
                              $object->transactions()->inYear($date)->sum(
                                  'amount'
                              )
                          )];
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