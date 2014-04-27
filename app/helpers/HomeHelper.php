<?php
/** @noinspection PhpIncludeInspection */
include_once('Toolkit.php');


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
        $key = 'homeAccountList'.$date->format('Ymd');
        if(Cache::has($key)) {
            return Cache::get($key);
        }
        $query = Auth::user()->accounts()->notHidden()->get();
        $accounts = [];

        foreach ($query as $account) {
            $url = URL::Route('accountoverviewmonth', [$account->id, $date->format('Y'), $date->format('m')]);
            $homeURL = URL::Route('home',[$date->format('Y'), $date->format('m'),$account->id]);
            $accounts[] = [
                'name'    => $account->name,
                'id'      => $account->id,
                'url'     => $url,
                'homeurl' => $homeURL,
                'current' => $account->balanceOnDate($date),
                'shared'  => $account->shared == 1 ? true : false,
            ];
        }

        unset($query);
        Cache::forever($key,$accounts);
        return $accounts;
    }

    /**
     * @param Carbon $date
     *
     * @return array
     */
    public static function budgetOverview(Carbon $date,Account $account)
    {
        $key = 'budgetOverview'.$date->format('Ymd').$account->id;
        if(Cache::has($key)) {
            return Cache::get($key);
        }
        $budgets = [];
        $transactions = $account->transactions()->expenses()->inMonth($date)
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
        $transfers = $account->transfersfrom()->orderBy('date', 'DESC')->orderBy('id', 'DESC')->inMonth($date)
        ->leftJoin('accounts','accounts.id','=','transfers.accountto_id')->
            where('accounts.shared',1)
            ->beforeDate($date)->get(['transfers.*']);
        foreach ($transfers as $t) {
            // get the budget
            if (!is_null($t->budget)) {
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
        Cache::forever($key,$budgets);
        return $budgets;

    }

    /**
     * @param Carbon $date
     *
     * @return array
     */
    public static function getAllowance(Carbon $date,Account $account)
    {
        $key = 'getAllowance'.$date->format('Ymd').$account->id;
        if(Cache::has($key)) {
            return Cache::get($key);
        }
        // get the allowance (setting) for this month, OR specific month.
        // and grab the value:
        $defaultAllowance = Setting::getSetting('defaultAllowance');
        $specificAllowance = Auth::user()->settings()->where('name', 'specificAllowance')->where(
            'date', $date->format('Y-m') . '-01'
        )->where('account_id',$account->id)->first();
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
                    $account->transactions()->inMonth($date)->expenses()->where('ignoreallowance', 0)->sum('amount')
                ) * -1;

            // also count transfers that went to a shared account:
            $spentOnShared = floatval(
                Auth::user()->transfers()->leftJoin('accounts', 'accounts.id', '=', 'transfers.accountto_id')->where(
                    'accounts.shared', 1
                )->where('accountto_id',$account->id)->inMonth($date)->where('ignoreallowance',0)->sum('amount')
            );

            // save it as the spent amount:
            $allowance['spent'] = $spent + $spentOnShared;
            // if we have overspent:
            if ($spent > $amount) {
                $allowance['over'] = true;
            }
            $allowance['pct'] = round(($spent / $amount) * 100);
        }
        Cache::forever($key,$allowance);
        return $allowance;
    }

    /**
     * @param Carbon $date
     *
     * @return array
     */
    public static function getPredictables(Carbon $date,Account $account)
    {
        $key = 'getPredictables'.$date->format('Ymd').$account->id;
        if(Cache::has($key)) {
            return Cache::get($key);
        }
        $predictables = $account->predictables()->active()->orderBy('dom', 'ASC')->get();
        $list = [];
        foreach ($predictables as $p) {
            $count = $p->transactions()->inMonth($date)->count();
            if ($count == 0) {
                $p->date = new Carbon('2012-01-' . $p->dom);
                $list[] = $p;
            }
        }
        Cache::forever($key,$list);
        return $list;
    }

    /**
     * @param Carbon $date
     *
     * @return mixed
     */
    public static function transactions(Carbon $date,Account $account)
    {
        return $account->transactions()->remember(20)->take(5)->orderBy('date', 'DESC')->orderBy('id', 'DESC')->inMonth($date)
            ->get();
    }

    /**
     * @param Carbon $date
     *
     * @return mixed
     */
    public static function transfers(Carbon $date,Account $account)
    {
        $from = $account->transfersfrom()->remember(20)->take(5)->orderBy('date', 'DESC')->orderBy('id', 'DESC')->inMonth($date)->get();
        $to = $account->transfersto()->take(5)->remember(20)->orderBy('date', 'DESC')->orderBy('id', 'DESC')->inMonth($date)->get();
        $result = $from->merge($to);
        return $result;
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