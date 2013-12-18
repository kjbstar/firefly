<?php
/**
 * Created by PhpStorm.
 * User: sander
 * Date: 13/12/13
 * Time: 21:00
 */
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
}