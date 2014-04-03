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
            $url = URL::Route('accountoverview', [$account->id, $date->format('Y'), $date->format('m')]);
            $accounts[] = [
                'name'    => $account->name,
                'url'     => $url,
                'current' => $account->balanceOnDate($date),
                'shared'  => $account->shared == 1 ? true : false,
            ];
        }

        unset($query);
        return $accounts;
    }

    /**
     * @param Carbon $date
     *
     * @return array
     */
    public static function budgetOverview(Carbon $date)
    {
        $budgets = [];
        $transactions = Auth::user()->transactions()->expenses()->inMonth($date)
            ->beforeDate($date)->get();
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
                    $budgets[$id] = ['name'  => $t->budget->name,
                                     'spent' => ($t->amount * -1)];
                    // limit:
                    $limit = $t->budget->limits()->inMonth($date)->first();
                    if ($limit) {
                        $budgets[$id]['limit'] = $limit->amount;
                    }

                }

            }
        }

        // Add transfers to shared accounts as expenses:
        $transfers = Auth::user()->transfers()->take(5)->orderBy('date', 'DESC')->orderBy('id', 'DESC')->inMonth($date)
            ->beforeDate($date)->get(['transfers.*']);
        foreach ($transfers as $t) {
            // get the budget
            if ($t->budget) {
                // basic budget info:
                $id = $t->budget->id;
                if (isset($budgets[$id])) {
                    // only add information
                    $budgets[$id]['spent'] += $t->amount;
                } else {
                    // create new one:
                    $budgets[$id] = ['name'  => $t->budget->name,
                                     'spent' => $t->amount];
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
            Log::debug('Spent for budget ' . $budget['name'] . ': ' . mf($budget['spent']));
            if (isset($budget['limit'])
                && $budget['limit'] < $budget['spent']
            ) {
                // overspent:
                $budgets[$id]['pct'] = ceil(($budget['limit'] / $budget['spent']) * 100);

            } elseif (isset($budget['limit'])
                && $budget['limit'] >= $budget['spent']
            ) {
                $budgets[$id]['pct'] = ceil(($budget['spent'] / $budget['limit']) * 100);

            }
        }

        // let's do some percentages:
        return $budgets;

    }

    /**
     * @param Carbon $date
     *
     * @return array
     */
    public static function getAllowance(Carbon $date)
    {
        // get the allowance (setting) for this month, OR specific month.
        // and grab the value:
        $defaultAllowance = Setting::getSetting('defaultAllowance');
        $specificAllowance = Auth::user()->settings()->where('name', 'specificAllowance')->where(
            'date', $date->format('Y-m') . '-01'
        )->first();
        $amount = !is_null($specificAllowance) ? $specificAllowance->value : $defaultAllowance->value;
        unset($specificAllowance, $defaultAllowance);

        // make the default array:
        // days = number of days left, used as a percentage:
        $allowance = [
            'amount' => $amount,
            'over'   => false,
            'spent'  => 0,
            'days'   => round((intval($date->format('d')) / intval($date->format('t'))) * 100)
        ];

        // start with the allowance thing,
        // if relevant:
        if ($amount > 0) {
            // get all transactions and ignore transactions from shared accounts:
            $spent = floatval(
                    Auth::user()->transactions()->inMonth($date)->expenses()->where('ignoreallowance', 0)->leftJoin(
                        'accounts', 'accounts.id', '=', 'transactions.account_id'
                    )->where('accounts.shared', 0)->sum('amount')
                ) * -1;

            // also count transfers that went to a shared account:
            $spentOnShared = floatval(
                Auth::user()->transfers()->leftJoin('accounts', 'accounts.id', '=', 'transfers.accountto_id')->where(
                    'accounts.shared', 1
                )->inMonth($date)->sum('amount')
            );

            // save it as the spent amount:
            $allowance['spent'] = $spent + $spentOnShared;
            // if we have overspent:
            if ($spent > $amount) {
                $allowance['over'] = true;
            }
            $allowance['pct'] = round(($spent / $amount) * 100);
        }

        return $allowance;
    }

    /**
     * @param Carbon $date
     *
     * @return array
     */
    public static function getPredictables(Carbon $date)
    {
        $predictables = Auth::user()->predictables()->active()->orderBy('dom', 'ASC')->get();
        $list = [];
        foreach ($predictables as $p) {
            $count = $p->transactions()->inMonth($date)->count();
            if ($count == 0) {
                $p->date = new Carbon('2012-01-' . $p->dom);
                $list[] = $p;
            }
        }
        return $list;
    }

    /**
     * @param Carbon $date
     *
     * @return mixed
     */
    public static function transactions(Carbon $date)
    {
        return Auth::user()->transactions()->take(5)->orderBy('date', 'DESC')->orderBy('id', 'DESC')->inMonth($date)
            ->get();
    }

    /**
     * @param Carbon $date
     *
     * @return mixed
     */
    public static function transfers(Carbon $date)
    {
        return Auth::user()->transfers()->take(5)->orderBy('date', 'DESC')->orderBy('id', 'DESC')->inMonth($date)->get(
        );
    }

    /**
     * @return array
     */
    public static function history()
    {
        $earliest = Toolkit::getEarliestEvent();
        $history = [];
        $now = new Carbon;
        $now->addMonth();
        while ($now > $earliest) {
            $url = URL::Route('home', [$now->format('Y'), $now->format('n')]);
            $history[] = [
                'url'     => $url,
                'title'   => $now->format('F Y'),
                'newline' => ($now->format('m') == '1') ? true : false,
            ];
            $now->subMonth();
        }
        return $history;
    }

}