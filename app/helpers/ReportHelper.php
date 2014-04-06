<?php
use Carbon\Carbon as Carbon;

/**
 * Class ReportHelper
 */
class ReportHelper
{

    public static function summary(Carbon $date, $period)
    {
        $start = clone $date;
        $start->subDay();
        $end = clone $date;
        switch ($period) {
            default:
            case 'month':
                $endOf = 'endOfMonth';
                $startOf = 'startOfMonth';
                $inPeriod = 'inMonth';
                break;
            case 'year':
                $endOf = 'endOfYear';
                $startOf = 'startOfYear';
                $inPeriod = 'inYear';
                break;
        }
        $date->$startOf();
        $end->$endOf();

        // get the incomes:
        $income = floatval(Auth::user()->transactions()->$inPeriod($date)->incomes()->sum('amount'));

        // get the expenses:
        $expenses = floatval(Auth::user()->transactions()->$inPeriod($date)->expenses()->sum('amount'));

        // received in total from shared accounts (this might be income):
        $receivedFromShared = floatval(
            Auth::user()->transfers()->leftJoin('accounts', 'accounts.id', '=', 'transfers.accountfrom_id')->where(
                'accounts.shared', 1
            )->$inPeriod(
                    $date
                )->sum('amount')
        );

        $sentToShared = floatval(
            Auth::user()->transfers()->leftJoin('accounts', 'accounts.id', '=', 'transfers.accountto_id')->where(
                'accounts.shared', 1
            )->$inPeriod(
                    $date
                )->sum('amount')
        );
        $shared = ($receivedFromShared - $sentToShared);

        // received more: income!
        if ($shared > 0) {
            $income += $shared;
        } else {
            // spent more, expense!
            $expenses -= $shared;
        }

        // get the net worth:
        $nwEnd = 0;
        $nwStart = 0;
        foreach (Auth::user()->accounts()->notHidden()->notShared()->get() as $account) {
            $nwEnd += $account->balanceOnDate($end);
            $nwStart += $account->balanceOnDate($start);
        }


        $data = [
            'income'   => [
                'income'  => $income,
                'expense' => $expenses,
            ],
            'networth' => [
                'start'     => $nwStart,
                'startdate' => $date,
                'end'       => $nwEnd,
                'enddate'   => $end
            ]
        ];

        return $data;
    }

    public static function biggestExpenses(Carbon $date, $period)
    {

        switch ($period) {
            default:
            case 'month':
                $inPeriod = 'inMonth';
                break;
            case 'year':
                $inPeriod = 'inYear';
                break;
        }
        $transactions = Auth::user()->transactions()->expenses()->orderBy('amount', 'ASC')->whereNull('predictable_id')
            ->take(10)->$inPeriod(
                $date
            )->get();
        $transfers = Auth::user()->transfers()->leftJoin('accounts', 'accounts.id', '=', 'transfers.accountto_id')
            ->where('accounts.shared', 1)->$inPeriod(
                $date
            )->get();
        $mutations = [];

        // we have both:
        if (count($transfers) > 0 && count($transactions) > 0) {
            $mutations = $transactions->merge($transfers);
            $mutations = $mutations->sortBy(
                function ($a) {
                    return $a->amount;
                }
            )->reverse();
        }
        // we have transactions:
        if (count($transfers) == 0 && count($transactions) > 0) {
            $mutations = $transactions;
        }
        // we have transfers:
        if (count($transactions) == 0 && count($transfers) > 0) {
            $mutations = $transfers;
        }

        return $mutations;
    }

    public static function predicted($date)
    {
        $transactions = Auth::user()->transactions()->expenses()->orderBy('amount', 'ASC')->whereNotNull(
            'predictable_id'
        )->take(10)->inMonth($date)->get();
        $transactions->each(
            function (Transaction $t) {
                $t->predicted = $t->predictable()->first()->amount;
            }
        );


        return $transactions;
    }

    public static function months(Carbon $date)
    {
        $date->startOfYear();
        $end = clone $date;
        $end->endOfYear();
        $current = clone $date;
        $list = [];

        while ($current <= $end) {

            $out = Auth::user()->transactions()->inMonth($current)->expenses()->sum('amount');
            $in = Auth::user()->transactions()->inMonth($current)->incomes()->sum('amount');

            $list[] = [
                'date' => $current->format('F Y'),
                'in'   => $in,
                'out'  => $out,
                'url'  => URL::Route('monthreport', [$current->format('Y'), $current->format('m')])
            ];

            $current->addMonth();
        }
        return $list;

    }

    public static function expenses(Carbon $date)
    {
        $data = [];
        $transactions = Auth::user()->transactions()->whereNull('predictable_id')->expenses()->with(
            ['components' => function ($query) {
                    $query->where('type', 'category');
                }]
        )->inMonth($date)->get();

        foreach ($transactions as $t) {
            $key = $t->category->id;
            if (isset($data[$key])) {
                $data[$key]['transactions'][] = $t;
            } else {
                $data[$key] = [
                    'category'     => [
                        'id'   => $key,
                        'name' => $t->category->name
                    ],
                    'transactions' => [$t]
                ];
            }
        }
        return $data;
    }

    public static function incomes(Carbon $date, $period)
    {
        switch ($period) {
            case 'month':
                $inPeriod = 'inMonth';
                break;
            case 'year':
                $inPeriod = 'inYear';
                break;
        }

        $data = [];
        $transactions = Auth::user()->transactions()->incomes()->with(
            ['components' => function ($query) {
                    $query->where('type', 'beneficiary');
                }]
        )->$inPeriod($date)->get();

        foreach ($transactions as $t) {
            $key = is_null($t->beneficiary) ? 0 : $t->beneficiary->id;
            if (isset($data[$key])) {
                $data[$key]['transactions'][] = $t;
            } else {
                $data[$key] = [
                    'beneficiary'  => [
                        'id'   => $key,
                        'name' => is_null($t->beneficiary) ? '(no beneficiary)' : $t->beneficiary->name
                    ],
                    'transactions' => [$t]
                ];
            }
        }
        return $data;
    }

    public static function budgets(Carbon $date)
    {
        $transactions = Auth::user()->transactions()->expenses()->with(
            ['components'        => function ($query) {
                    $query->where('type', 'budget');
                },
             'components.limits' => function ($query) use ($date) {
                     $query->inMonth($date);
                 }
            ]
        )->inMonth($date)->get();
        $budgets = [];
        foreach ($transactions as $t) {
            $key = is_null($t->budget) ? 0 : $t->budget->id;

            if (isset($budgets[$key])) {
                $budgets[$key]['amount'] += $t->amount;
            } else {
                $budgets[$key] = [
                    'budget' => $t->budget,
                    'amount' => $t->amount
                ];
            }
        }
        return $budgets;

    }
}