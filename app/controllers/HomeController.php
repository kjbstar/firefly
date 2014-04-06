<?php
/** @noinspection PhpIncludeInspection */
include_once(app_path() . '/helpers/HomeHelper.php');
/** @noinspection PhpIncludeInspection */
include_once(app_path() . '/helpers/Toolkit.php');

use Carbon\Carbon as Carbon;

/**
 * Class HomeController
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
     * @param int $year The year
     * @param int $month The month
     *
     * @return View
     */
    public function showHome($year = null, $month = null)
    {

        $today = Toolkit::parseDate($year, $month, new Carbon);
        $actual = new Carbon;
        // fix $today if it's in this month:
        if ($actual->format('Y-m') == $today->format('Y-m')) {
            $today = new Carbon;

        }
        unset($actual);


        $fpAccount = Toolkit::getFrontpageAccount();

        // get all kinds of lists:
        $accounts = HomeHelper::homeAccountList($today);
        $allowance = HomeHelper::getAllowance($today);
        $predictables = HomeHelper::getPredictables($today);
        $budgets = HomeHelper::budgetOverview($today);
        $transactions = HomeHelper::transactions($today);
        $transfers = HomeHelper::transfers($today);
        $history = HomeHelper::history();

        return View::make('home.home')->with('title', 'Home')->with('accounts', $accounts)->with('today', $today)->with(
            'history', $history
        )->with('allowance', $allowance)->with('transactions', $transactions)->with('fpAccount', $fpAccount)->with(
            'budgets', $budgets
        )->with('predictables', $predictables)->with('transfers', $transfers);
    }

    /**
     * TODO: catch no accounts present.
     *
     * @param $year
     * @param $month
     * @param $day
     *
     * @return \Illuminate\View\View
     */
    public function predict($year, $month, $day)
    {
        $date = new Carbon($year . '-' . $month . '-' . $day);
        $account = Toolkit::getFrontpageAccount();
        $prediction = $account->predictOnDateExpanded($date);

        // do a prediction, but "visible":
        return View::make('home.predict')->with('prediction', $prediction)
            ->with('date', $date);
    }
}
