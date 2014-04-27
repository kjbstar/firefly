<?php

use Carbon\Carbon as Carbon;

/**
 * Class Toolkit
 */
class Toolkit
{
    /**
     * Parse a year and a date into a carbon object.
     *
     * @param int    $year    The year
     * @param int    $month   The month
     * @param Carbon $default The default date to return, if any.
     *
     * @return Carbon
     */
    public static function parseDate($year, $month, Carbon $default = null)
    {
        if (!is_null($year) && !is_null($month) && $year != ''
            && $month != ''
        ) {
            $year = intval($year);
            $month = intval($month);
            if ($year > 2000 && $year <= 3000 && $month >= 1 && $month <= 12) {
                return new Carbon($year . '-' . $month . '-01');
            }
        }

        return $default;
    }

    /**
     * @return mixed
     */
    public static function getFrontpageAccount()
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $key = 'frontPageAccount';
        if (Cache::has($key)) {
            return Cache::get($key);
        } else {
            $frontpageAccount = Setting::getSetting('frontpageAccount');
            if ($frontpageAccount->value == '') {
                $account = Auth::user()->accounts()->first();
            } else {
                $account = Auth::user()->accounts()->find($frontpageAccount->value);
            }
            Cache::put($key, $account, 10080);
            return $account;
        }
    }

    /**
     * @return string
     */
    public static function getPredictionStart()
    {
        $setting = Setting::getSetting('predictionStart');
        return $setting->value;

    }

    /**
     * @return Carbon|mixed
     */
    public static function getEarliestEvent()
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $key = 'getEarliestEvent';
        if (Cache::has($key)) {
            return Cache::get($key);
        } else {
            $account = Auth::user()->accounts()->orderBy(
                'openingbalancedate', 'ASC'
            )->first();
            if ($account) {
                $date = $account->openingbalancedate;
            } else {
                $date = new Carbon;
            }
            Cache::put($key, $date, 10080);
            return $date;
        }

    }
}