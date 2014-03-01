<?php
/** @noinspection PhpIncludeInspection */
include_once(app_path() . '/helpers/HomeHelper.php');
/** @noinspection PhpIncludeInspection */
include_once(app_path() . '/helpers/Toolkit.php');

use Carbon\Carbon as Carbon;

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
        $earliest = Toolkit::getEarliestEvent();
        $today = Toolkit::parseDate($year, $month, new Carbon);
        $fpAccount = Toolkit::getFrontpageAccount();

        // get all kinds of lists:
        $accounts = HomeHelper::homeAccountList($today);
        $allowance = HomeHelper::getAllowance($today);
        // budget overview.
        $budgets = HomeHelper::bugetOverview($today);
        $transactions = Auth::user()->transactions()->take(5)->orderBy(
            'date', 'DESC'
        )->inMonth($today)->get();

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
            )->with('allowance', $allowance)->with(
                'transactions', $transactions
            )->with('fpAccount', $fpAccount)->with('budgets',$budgets);
    }
}
