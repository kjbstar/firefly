<?php
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

}