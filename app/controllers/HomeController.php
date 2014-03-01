<?php
include_once(app_path() . '/helpers/HomeHelper.php');
include_once(app_path() . '/helpers/Toolkit.php');
/**
 * File contains the HomeController.
 *
 * PHP version 5.5.6
 *
 * @category Controllers
 * @package  Controllers
 * @author   Sander Dorigo <sander@dorigo.nl>
 * @license  GPL 3.0
 * @link     http://geld.nder.dev/
 */
use Carbon\Carbon as Carbon;

/**
 * Class HomeController
 *
 * @category AccountController
 * @package  Controllers
 * @author   Sander Dorigo <sander@dorigo.nl>
 * @license  GPL 3.0
 * @link     http://www.sanderdorigo.nl/
 */
class HomeController extends BaseController
{


    /**
     * Shows the index page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function showIndex()
    {

        if (Auth::check()) {
            return Redirect::to('/home');
        } else {
            return Redirect::to('/login');
        }
    }

    /**
     * Show the homepage. Can be for another month.
     *
     * @param int $year  The year
     * @param int $month The month
     *
     * @return View
     */
    public function showHome($year = null, $month = null)
    {
        $earliest = Toolkit::getEarliestEvent();
        $today = Toolkit::parseDate($year, $month, new Carbon);
        $fpAccount = Toolkit::getFrontpageAccount();

        // get all kinds of lists:
        $accounts = HomeHelper::homeAccountList($today);
        $allowance = HomeHelper::getAllowance($today);

        // build a history:
        $history = [];
        $now = new Carbon;
        $now->addMonth();

        while ($now > $earliest) {

            $url = URL::Route(
                'home', [$now->format('Y'), $now->format('n')]
            );
            $entry = [];
            $entry['url'] = $url;
            $entry['title'] = $now->format('F Y');
            $history[] = $entry;
            $now->subMonth();
        }

        return View::make('home.home')->with('title', 'Home')->with(
            'accounts', $accounts
        )->with('today', $today)->with(
                'history', $history
            )->with('allowance', $allowance)->with('fpAccount',$fpAccount);
    }
}
