<?php
/** @noinspection PhpIncludeInspection */
include_once(app_path() . '/helpers/Toolkit.php');


use Carbon\Carbon as Carbon;

/**
 * Class HomeHelper
 */
class HomeHelper
{
    /**
     * Returns a list of active accounts for a given month to be used on
     * the home page.
     *
     * @param Carbon $date
     *
     * @return array
     */
    public static function homeAccountList(Carbon $date)
    {
        $query = Auth::user()->accounts()->notHidden()->get();
        $accounts = [];

        foreach ($query as $account) {
            $url = URL::Route(
                'accountoverview',
                [$account->id, $date->format('Y'), $date->format('m')]
            );

            $entry = [];
            $entry['name'] = $account->name;
            $entry['url'] = $url;
            $entry['current'] = $account->balanceOnDate($date);
            $accounts[] = $entry;
        }

        unset($query, $entry);

        return $accounts;
    }

    public static function bugetOverview(Carbon $date)
    {
        $budgets = [];
        $transactions = Auth::user()->transactions()->expenses()->inMonth($date)
            ->get();
        foreach ($transactions as $t) {
            // get the budget
            if ($t->budget) {
                // basic budget info:
                $id = $t->budget->id;
                if (isset($budgets[$id])) {
                    // only add information
                    $budgets[$id]['spent'] += ($t->amount * -1);
                } else {
                    // create new one:
                    $budgets[$id] = ['name' => $t->budget->name,
                                     'spent' => ($t->amount * -1)];
                    // limit:
                    $limit = $t->budget->limits()->inMonth($date)->first();
                    if ($limit) {
                        $budgets[$id]['limit'] = $limit->amount;
                    }

                }

            }
        }
        // loop budgets for percentages:
        foreach ($budgets as $id => $budget) {
            if (isset($budget['limit'])
                && $budget['limit'] < $budget['spent']
            ) {
                // overspent:
                $budgets[$id]['pct'] = ceil(($budget['limit'] / $budget['spent'])*100);

            } elseif (isset($budget['limit'])
                && $budget['limit'] >= $budget['spent']
            ) {
                $budgets[$id]['pct'] = ceil(($budget['spent'] / $budget['limit'])*100);

            }
        }

        // let's do some percentages:
        return $budgets;

    }

    public static function getAllowance(Carbon $date)
    {
        // default values and array
        $defaultAllowance = Setting::getSetting('defaultAllowance');
        $specificAllowance = Auth::user()->settings()->where(
            'name', 'specificAllowance'
        )->where('date', $date->format('Y-m') . '-01')->first();
        $allowance = !is_null($specificAllowance) ? $specificAllowance
            : $defaultAllowance;

        $amount = floatval($allowance->value);
        $allowance = ['amount' => $amount, 'over' => false, 'spent' => 0];
        $days = round(
            (intval($date->format('d')) / intval(
                    $date->format('t')
                )) * 100
        );
        $allowance['days'] = $days;
        // start!
        if ($amount > 0) {
            $spent = floatval(
                    Auth::user()->transactions()->inMonth($date)->expenses()
                        ->where('ignoreallowance', 0)->sum('amount')
                ) * -1;
            $allowance['spent'] = $spent;
            // overspent this allowance:
            if ($spent > $amount) {
                $allowance['over'] = true;
                $allowance['pct'] = round(($amount / $spent) * 100);
            }
            // did not overspend this allowance.
            if ($spent <= $amount) {
                $allowance['pct'] = round(($spent / $amount) * 100);
            }
        }

        return $allowance;
    }

    public static function getPredictables(Carbon $date) {
        $predictables = Auth::user()->predictables()->active()->orderBy('dom','ASC')->get();
        $list = [];
        foreach($predictables as $p) {
            $count = $p->transactions()->inMonth($date)->count();
            if($count == 0) {
                $p->date = new Carbon('2012-01-'.$p->dom);
                $list[] = $p;
            }
        }
        return $list;
    }

}