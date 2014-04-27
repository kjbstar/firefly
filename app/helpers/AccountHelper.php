<?php
use Carbon\Carbon as Carbon;

/**
 * Class AccountHelper
 */
class AccountHelper
{

    /**
     * Gets a list of accounts used in a account select list. Excludes hidden
     * ones.
     *
     * @return array
     */
    public static function accountsAsSelectList()
    {
        $accounts = [];
        foreach (Auth::user()->accounts()->notHidden()->get() as $a) {
            $accounts[$a->id] = $a->name;
        }

        return $accounts;
    }

    /**
     * @param Account $account
     *
     * @return array
     */
    public static function months(Account $account)
    {
        $end = new Carbon;
        $end->firstOfMonth();
        $start = Toolkit::getEarliestEvent();
        $list = [];
        while ($end >= $start) {
            $url = URL::Route(
                'accountoverviewmonth', [$account->id, $end->format('Y'), $end->format('m')]
            );
            $list[] = [
                'url'     => $url,
                'title'   => $end->format('F Y'),
                'date'    => clone $end,
                'balance' => $account->balanceOnDate($end)
            ];
            $end->subMonth();
        }


        return $list;
    }

    /**
     * @param Account $account
     * @param Carbon  $date
     *
     * @return mixed
     */
    public static function mutations(Account $account, Carbon $date)
    {
        $transactions = $account->transactions()->inMonth($date)->get();
        $transfersTo = $account->transfersto()->inMonth($date)->get();
        $transfersFrom = $account->transfersfrom()->inMonth($date)->get();
        $result = $transactions->merge($transfersFrom);
        $result = $result->merge($transfersTo);
        $result = $result->sortBy(
            function ($a) {
                return $a->created_at;
            }
        )->reverse();
        return $result;
    }

    public static function getPredictionStart()
    {
        $predictionStart = Setting::getSetting('predictionStart');
        return $predictionStart->value;
    }

    /**
     * Returns the transactions marked in this period. One per date max.
     *
     * @param Account $account
     * @param Carbon  $start
     * @param Carbon  $end
     *
     * @return array
     */
    public static function getMarkedTransactions(
        Account $account, Carbon $start, Carbon $end
    ) {
        $now = new Carbon;
        if ($now->diffInMonths($start) > 2) {
            $cacheTime = 20160;
        } else {
            $cacheTime = 1440;
        }
        /** @noinspection PhpUndefinedFieldInspection */
        $key = $account->id . 'marked' . $start->format('Ymd') . $end->format('Ymd') . 'Marked';
        if (Cache::has($key)) {
            return Cache::has($key);
        } else {

            $transactions = $account->transactions()->where(
                'mark', 1
            )->betweenDates($start, $end)->get();
            $marked = [];
            foreach ($transactions as $t) {
                $theDate = $t->date->format('Y-m-d');
                $marked[$theDate] = [$t->description,
                                     $t->description . ': EUR ' . $t->amount];
            }
            Cache::put($key, $marked, $cacheTime);
            return $marked;
        }
    }

    /**
     * @return array
     */
    public static function emptyPrefilledAray()
    {
        return [
            'name'               => '',
            'openingbalance'     => '',
            'openingbalancedate' => date('Y-m-d'),
            'hidden'             => false,
            'shared'             => false
        ];
    }

    /**
     * @return array
     */
    public static function prefilledFromOldInput()
    {
        return [
            'name'               => Input::old('name'),
            'openingbalance'     => Input::old('openingbalance'),
            'openingbalancedate' => Input::old('openingbalancedate'),
            'hidden'             => intval(Input::old('hidden')) == 1 ? true : false,
            'shared'             => intval(Input::old('shared')) == 1 ? true : false
        ];
    }

    /**
     * @param Account $account
     *
     * @return array
     */
    public static function prefilledFromAccount(Account $account)
    {
        return [
            'name'               => $account->name,
            'openingbalance'     => $account->openingbalance,
            'openingbalancedate' => $account->openingbalancedate->format('Y-m-d'),
            'hidden'             => $account->hidden == 1 ? true : false,
            'shared'             => $account->shared == 1 ? true : false,

        ];
    }

} 