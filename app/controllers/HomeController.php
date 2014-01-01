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
        $earliest = HomeHelper::getEarliestEvent();
        $today = Toolkit::parseDate($year, $month, new Carbon);

        // get all kinds of lists:
        $accounts = HomeHelper::homeAccountList($today);
        $transactions = HomeHelper::homeTransactionList($today);
        $transfers = HomeHelper::homeTransferList($today);
        $budgets = HomeHelper::homeComponentList('budget', $today);
        $categories = HomeHelper::homeComponentList('category', $today);
        $beneficiaries = HomeHelper::homeComponentList('beneficiary', $today);
        $accountCount = Auth::user()->accounts()->count();
        $transactionCount = Auth::user()->transactions()->count();

        // resort the component lists:
        $amount = [];
        foreach ($budgets as $key => $row) {
            $amount[$key] = $row['amount'];
        }
        array_multisort($amount, SORT_ASC, $budgets);

        $amount = [];
        foreach ($categories as $key => $row) {
            $amount[$key] = $row['amount'];
        }
        array_multisort($amount, SORT_ASC, $categories);

        $amount = [];
        foreach ($beneficiaries as $key => $row) {
            $amount[$key] = $row['amount'];
        }
        array_multisort($amount, SORT_ASC, $beneficiaries);

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

        // get some budgetary information:
        $budgetInfo = HomeHelper::getBudgetaryInformation($today);


        return View::make('home.home')->with('title', 'Home')->with(
            'accounts', $accounts
        )->with('today', $today)->with(
                'budgets', $budgets
            )->with('transactions', $transactions)->with(
                'transfers', $transfers
            )->with('beneficiaries', $beneficiaries)->with(
                'categories', $categories
            )->with(
                'history', $history
            )->with('accountCount', $accountCount)->with(
                'transactionCount', $transactionCount
            )->with(
                'budgetInfo', $budgetInfo
            );
    }

    /**
     * Displays the chart on the homepage for the indicated type
     *
     * @param string $type
     * @param int    $year
     * @param int    $month
     *
     * @return string
     */
    public function showChart($type, $year = null, $month = null)
    {
        switch ($type) {
            case 'beneficiaries':
            case 'beneficiary':
            case 'categories':
            case 'category':
            case 'budgets':
            case 'budget':
                return Response::json(
                    HomeHelper::homeComponentChart($type, $year, $month)
                );
                break;
            case 'accounts':
                return Response::json(
                    HomeHelper::homeAccountChart($year, $month)
                );
                break;
            default:
                return Response::json(false);
                break;
        }


    }

}
