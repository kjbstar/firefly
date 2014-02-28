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
     * Generates a list of transactions in the month indicated by $date
     *
     * @param Account $account The account
     * @param Carbon  $date    THe date
     *
     * @return array
     */
    public static function generateTransactionListByMonth(
        Account $account, Carbon $date
    ) {
        return $account->transactions()->orderBy('date', 'DESC')->inMonth($date)
            ->get();
    }

    /**
     * Tries to predict the expenses for this account.
     *
     * @param Account $account The account
     *
     * @return array
     */
    public static function generatePredictions(Account $account)
    {
        // starting NOW to the end of the month.
        $now = new Carbon;
        $now->addDay();
        $now->startOfMonth();
        $eom = clone $now;
        $eom->endOfMonth();
        $predictions = [];
        $balance = $account->balanceOnDate($now);
        $predictionStart = Setting::getSetting('predictionStart');
        while ($now < $eom) {
            // get the predicted value:
            $entry = [];
            $entry['transactions'] = [];
            $entry['balance'] = $balance;
            $entry['date'] = $now->format('l d F Y');
            $data = $account->predictOnDate($now);
            $entry['prediction'] = $data['prediction'];
            $balance -= $data['prediction'];

            $entry['end-balance'] = $balance;

            // transactions for this day:
            $entry['transactions'] = $account->transactions()->onDayOfMonth(
                $now
            )->expenses()->where('date', '>=', $predictionStart->value)->get();
            $predictions[] = $entry;
            $now->addDay();
        }

        return $predictions;
    }

    /**
     * Generates a list of months and the balances during that month.
     *
     * @param Account $account The account.
     *
     * @return array
     */
    public static function generateOverviewOfMonths(Account $account)
    {
        $end = new Carbon;
        $end->firstOfMonth();
        $start = Toolkit::getEarliestEvent();
        $list = [];
        while ($end >= $start) {

            // money in:
            $url = URL::Route(
                'accountoverview',
                [$account->id, $end->format('Y'), $end->format('m')]
            );
            $entry = [];
            $entry['url'] = $url;
            $entry['title'] = $end->format('F Y');
            $entry['balance_start'] = $account->balanceOnDate($end);
            $end->subMonth();
            $list[] = $entry;
        }

        return $list;
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
        $transactions = $account->transactions()->where(
            'mark', 1
        )->betweenDates($start, $end)->get();
        $marked = [];
        foreach ($transactions as $t) {
            $theDate = $t->date->format('Y-m-d');
            $marked[$theDate] = [$t->description,
                                 $t->description . ': EUR ' . $t->amount];
        }

        return $marked;
    }

} 