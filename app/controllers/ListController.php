<?php
require_once(app_path() . '/helpers/ListHelper.php');
require_once(app_path() . '/helpers/Toolkit.php');
/**
 * ListController for Mosquito.
 *
 * PHP version 5
 *
 * @category Controllers
 * @package  Controllers
 * @author   Sander Dorigo <sander@dorigo.nl>
 * @license  GPL 3.0
 * @version  GIT: geld.nder.dev
 * @link     http://www.sanderdorigo.nl/
 *
 */


/**
 * This class handles all List related actions.
 *
 * @category AccountController
 * @package  Controllers
 * @author   Sander Dorigo <sander@dorigo.nl>
 * @license  GPL 3.0
 * @link     http://www.sanderdorigo.nl/
 */
class ListController extends BaseController
{

    /**
     * Shows a list of relevant transactions
     * for the selected component AND the
     * selected date.
     *
     * @param int    $componentID The ID
     * @param int    $year        The year
     * @param int    $month       The month
     * @param string $type        The type of component.
     *
     * @return View
     */
    public function showList($componentID, $year, $month, $type)
    {
        $date = Toolkit::parseDate($year, $month);
        $component = Auth::user()->components()->find($componentID);
        if (is_null($date)) {
            App::abort(404);
        }
        $list = [];
        if (is_null($component)) {
            $list = ListHelper::transactionsWithoutComponentType($type, $date);
        }
        if (!is_null($component)) {
            $list = ListHelper::transactionsWithComponent($component, $date);
        }

        // loop again to get the sum:
        $sum = 0;
        foreach ($list as $transaction) {
            $sum += $transaction->amount;
        }

        // loop again to get the percentages:
        foreach ($list as $transaction) {
            $pct = ($transaction->amount / $sum) * 100;
            $pct = $pct < 0 ? $pct * -1 : $pct;
            if ($pct < 3) {
                $transaction->pct = round($pct, 2);
            } else {
                $transaction->pct = round($pct, 0);
            }

        }

        return View::make('list.transactions_simple')->with(
            'transactions', $list
        )->with('sum', $sum);
    }
}