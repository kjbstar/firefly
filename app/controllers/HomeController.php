<?php
// @codeCoverageIgnoreStart
/** @noinspection PhpIncludeInspection */
include_once(app_path() . '/helpers/HomeHelper.php');
/** @noinspection PhpIncludeInspection */
include_once(app_path() . '/helpers/Toolkit.php');
// @codeCoverageIgnoreEnd

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
    public function index()
    {

        if (Auth::check()) {
            return Redirect::to('/home');
        } else {
            return Redirect::to('/login');
        }
    }

    /**
     * Show the homepage. Can be for another month or account.
     *
     * @param null    $year
     * @param null    $month
     * @param Account $fpAccount
     *
     * @return \Illuminate\View\View
     */
    public function home($year = null, $month = null, Account $fpAccount = null)
    {
        $today = Toolkit::parseDate($year, $month, new Carbon);
        $actual = new Carbon;
        // fix $today if it's in this month:
        if ($actual->format('Y-m') == $today->format('Y-m')) {
            $today = new Carbon;

        }

        unset($actual);
        /**
         * Instead of having a "frontpage" account, the
         * frontpage account setting is a default, and can be substituted for
         * any account there is. This means that an account can have a limit
         * as well, signifying the fact that an allowance can be set for each
         * one.
         */
        if (is_null($fpAccount)) {
            $fpAccount = Toolkit::getFrontpageAccount();
        }
        // get all kinds of lists:
        $accounts = HomeHelper::homeAccountList($today);
        $allowance = HomeHelper::getAllowance($today, $fpAccount);
        $predictables = HomeHelper::getPredictables($today, $fpAccount);
        $budgets = HomeHelper::budgetOverview($today, $fpAccount);
        $transactions = HomeHelper::transactions($today, $fpAccount);

        $transfers = HomeHelper::transfers($today, $fpAccount);

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
        $input = [
            'balance'     => floatval(Input::get('balance')),
            'pessimistic' => floatval(Input::get('pessimistic')),
            'optimistic'  => floatval(Input::get('optimistic')),
            'alt1'        => floatval(Input::get('alt1')),
            'alt2'        => floatval(Input::get('alt2')),

        ];


        $date = new Carbon($year . '-' . $month . '-' . $day);
        $account = Toolkit::getFrontpageAccount();
        $prediction = $account->predictOnDateExpanded($date);

        // do a prediction, but "visible":
        return View::make('home.predict')->with('prediction', $prediction)
            ->with('date', $date)->with('input', $input);
    }
}
