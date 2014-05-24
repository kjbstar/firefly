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
        /** @var $accountHelper \Firefly\Helper\Account\AccountHelperInterface */
        $accountHelper = App::make('Firefly\Helper\Account\AccountHelperInterface');

        $key = 'homeAccountList' . $date->format('Ymd');
        if (Cache::has($key)) {
            return Cache::get($key);
        }
        $query = Auth::user()->accounts()->notInactive()->get();
        $accounts = [];

        foreach ($query as $account) {
            $url = URL::Route('accountoverviewmonth', [$account->id, $date->format('Y'), $date->format('m')]);
            $homeURL = URL::Route('home', [$date->format('Y'), $date->format('m'), $account->id]);
            $accounts[] = [
                'name'    => $account->name,
                'id'      => $account->id,
                'url'     => $url,
                'homeurl' => $homeURL,
                'current' => $accountHelper->balanceOnDate($account,$date),
                'shared'  => $account->shared == 1 ? true : false,
            ];
        }

        unset($query);
        Cache::forever($key, $accounts);
        return $accounts;
    }

    /**
     * @param Carbon $date
     *
     * @return array
     */
    public static function budgetOverview(Carbon $date, Account $account = null)
    {
        if (is_null($account)) {
            return [];
        }
        $key = 'budgetOverview' . $date->format('Ymd') . $account->id;
        if (Cache::has($key)) {
            return Cache::get($key);
        }

        $budgetType = Type::where('type', 'budget')->first();
        $budgets = Auth::user()->components()->orderBy('parent_component_id', 'DESC')->where('type_id', $budgetType->id)
            ->get();
        $result = [];
        /** @var $budget Budget */
        foreach ($budgets as $budget) {
            $id = $budget->id;
            $current = [
                'name'       => $budget->name,
                'parentName' => $budget->parentComponent()->first() ? $budget->parentComponent->name : null,
                'limit'      => 0,
                'overspent'  => false,
                'pct'        => 100,
                'iconTag'    => $budget->iconTag()
            ];

            // find transactions in this month:
            $transactions = $budget->transactions()->where('account_id', $account->id)->inMonth($date)->sum('amount');
            $current['expense'] = floatval($transactions);

            // transactions count as expense when they go TO a shared account:
            $transfers = $budget->transfers()->leftJoin('accounts', 'accounts.id', '=', 'transfers.accountto_id')
                ->where('accounts.shared', 1)->where('transfers.accountfrom_id', $account->id)->inMonth($date)->sum(
                    'amount'
                );
            $current['expense'] += floatval($transfers) * -1;

            // has budget a limit in this month?
            $limit = $budget->limits()->inMonth($date)->where(
                function($query) use ($account) {
                $query->orWhereNull('account_id');
                    if(!is_null($account)) {
                        $query->orWhere('account_id',$account->id);
                    }
                }
            )->orderBy('account_id','DESC')->first();

            if (!is_null($limit)) {
                $current['limit'] = floatval($limit->amount);
                // overspent?
                if ($current['expense'] * -1 > $current['limit']) {
                    $current['overspent'] = true;
                    // calculate bar percentage:
                    $current['pct'] = round(($current['limit'] / $current['expense']) * -100);
                } else {
                    $current['overspent'] = false;
                    // calculate bar percentage:
                    $current['pct'] = round(($current['expense'] / $current['limit']) * -100);
                }
            }
            if ($current['expense'] != 0 || $current['limit'] != 0) {
                $result[$id] = $current;
            }
        }
        // now do the same for transactions + transfers without a budget!
        $result[0] = [
            'name'      => '(no budget)',
            'limit'     => 0,
            'overspent' => false,
            'pct'       => 100,
            'iconTag'   => ''
        ];
        /**
         * select * from transactions
         * where id not in (
         * select transaction_id from component_transaction
         * left join components ON components.id = component_transaction.component_id
         * where components.type_id=3
         * )
         */
        $transactions = Auth::user()->transactions()->where('account_id',$account->id)->whereNotIn(
            'id', function ($query) use ($date) {
                $query->select('transaction_id')->from('component_transaction')
                    ->leftJoin('components', 'components.id', '=', 'component_transaction.component_id')
                    ->leftJoin('transactions', 'transactions.id', '=', 'component_transaction.transaction_id')
                    ->where('transactions.amount', '<', 0)
                    ->where(DB::Raw('DATE_FORMAT(transactions.date,"%m-%Y")'), '=', $date->format('m-Y'))
                    ->where('components.type_id', 3);
            }
        )->inMonth($date)->expenses()->sum('amount');
        $result[0]['expense'] = floatval($transactions);


        Cache::forever($key, $result);
        return $result;

    }

    /**
     * @param Carbon $date
     *
     * @return array
     */
    public static function getAllowance(Carbon $date, Account $account = null)
    {

        // make the default array:
        // days = number of days left, used as a percentage:
        $allowance = [
            'amount' => 0,
            'over'   => false,
            'spent'  => 0,
            'days'   => round((intval($date->format('d')) / intval($date->format('t'))) * 100)
        ];
        if (is_null($account)) {
            return null;
        }

        $key = 'getAllowance' . $date->format('Ymd') . $account->id;
        if (Cache::has($key)) {
            return Cache::get($key);
        }


        // get the allowance (setting) for this month, OR specific month.
        // and grab the value:
        $defaultAllowance = Setting::getSetting('defaultAllowance');
        $specificAllowance = Auth::user()->settings()->where('name', 'specificAllowance')->where(
            'date', $date->format('Y-m') . '-01'
        )->where('account_id', $account->id)->first();
        $amount = !is_null($specificAllowance) ? $specificAllowance->value : $defaultAllowance->value;
        unset($specificAllowance, $defaultAllowance);

        $allowance['amount'] = $amount;

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
                )->where('accountfrom_id', $account->id)->inMonth($date)->where('ignoreallowance', 0)->sum('amount')
            );

            // if this account is shared, transfers away from it are expenses:
            $spentInTransfers = 0;
            if(!is_null($account) && $account->shared == 1) {
                $spentInTransfers = floatval($account->transfersfrom()->inMonth($date)->where('ignoreallowance',0)->sum('amount'));
            }

            // save it as the spent amount:
            $allowance['spent'] = $spent + $spentOnShared + $spentInTransfers;
            // if we have overspent:
            if ($allowance['spent'] > $amount) {
                $allowance['over'] = true;
            }
            $allowance['pct'] = round(($allowance['spent'] / $amount) * 100);
        }
        Cache::forever($key, $allowance);
        return $allowance;
    }

    /**
     * @param Carbon $date
     *
     * @return array
     */
    public static function getPredictables(Carbon $date, Account $account = null)
    {
        if (is_null($account)) {
            return [];
        }
        $key = 'getPredictables' . $date->format('Ymd') . $account->id;
        if (Cache::has($key)) {
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
        Cache::forever($key, $list);
        return $list;
    }

    /**
     * @param Carbon $date
     *
     * @return mixed
     */
    public static function transactions(Carbon $date, Account $account = null)
    {
        if (is_null($account)) {
            return [];
        }
        return $account->transactions()->rememberForever()->with('account')->take(5)->orderBy('date', 'DESC')->orderBy('id', 'DESC')
            ->inMonth($date)
            ->get();
    }

    /**
     * @param Carbon $date
     *
     * @return mixed
     */
    public static function transfers(Carbon $date, Account $account = null)
    {
        if (is_null($account)) {
            return [];
        }
        $from = $account->transfersfrom()->rememberForever()->with(['accountto', 'accountfrom'])->take(5)->orderBy(
            'date', 'DESC'
        )->orderBy('id', 'DESC')->inMonth($date)->get();
        $to = $account->transfersto()->take(5)->rememberForever()->with(['accountto', 'accountfrom'])->orderBy(
            'date', 'DESC'
        )->orderBy('id', 'DESC')->inMonth($date)->get();
        $result = $from->merge($to);
        return $result;
    }

    /**
     * @return array
     */
    public static function history(Account $account = null)
    {
        $earliest = Toolkit::getEarliestEvent();
        $history = [];
        $now = new Carbon;
        $now->addMonth();
        while ($now > $earliest) {
            if(!is_null($account)) {
                $url = URL::Route('home', [$now->format('Y'), $now->format('n'),$account->id]);
            } else {
                $url = URL::Route('home', [$now->format('Y'), $now->format('n')]);
            }
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