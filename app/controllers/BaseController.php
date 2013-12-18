<?php
/**
 * BaseController class that has some Laravel stuff in it.
 *
 * PHP version 5.5.6
 *
 * @category Controllers
 * @package  Controllers
 * @author   Sander Dorigo <sander@dorigo.nl>
 * @license  GPL 3.0
 * @link     http://geld.nder.dev/
 *
 */

/**
 * Class BaseController
 *
 * @category AccountController
 * @package  Controllers
 * @author   Sander Dorigo <sander@dorigo.nl>
 * @license  GPL 3.0
 * @link     http://www.sanderdorigo.nl/
 */
class BaseController extends Controller
{

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout()
    {
        if (!is_null($this->layout)) {
            $this->layout = View::make($this->layout);
        }
    }

//    /**
//     * Parse a year and a date into a carbon object.
//     *
//     * @param int $year  The year
//     * @param int $month The month
//     *
//     * @return Carbon
//     */
//    protected function parseDate($year, $month)
//    {
//        if (!is_null($year) && !is_null($month) && $year != ''
//            && $month != ''
//        ) {
//            $year = intval($year);
//            $month = intval($month);
//            if ($year > 2000 && $year <= 3000 && $month >= 1 && $month <= 12) {
//                return new Carbon($year . '-' . $month . '-01');
//            }
//        }
//
//        return null;
//    }

}
