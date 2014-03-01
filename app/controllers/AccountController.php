<?php
require_once(app_path() . '/helpers/AccountHelper.php');
require_once(app_path() . '/helpers/Toolkit.php');
use Carbon\Carbon as Carbon;

class AccountController extends BaseController
{

    /**
     * Shows the index page.
     *
     * @return View
     */
    public function showIndex()
    {
        $accounts = Auth::user()->accounts()->get()->each(
            function ($account) {
                $account->today = $account->balanceOnDate(new Carbon);
            }
        );

        return View::make('accounts.index')->with('accounts', $accounts)->with(
            'title', 'All accounts'
        );
    }

    /**
     * Shows the view to add a new account.
     *
     * @return \Illuminate\View\View
     */
    public function add()
    {
        Session::put('previous', URL::previous());

        return View::make('accounts.add')->with(
            'title', 'Add account'
        );
    }

    /**
     * Post process the addition of the Account.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postAdd()
    {
        $data = [];

        $data['name'] = Input::get('name');
        $data['openingbalance'] = floatval(Input::get('openingbalance'));
        $data['currentbalance'] = floatval(Input::get('openingbalance'));
        $data['openingbalancedate'] = Input::get('openingbalancedate');
        $data['user_id'] = Auth::user()->id;
        $data['hidden'] = Input::get('hidden') == '1' ? 1 : 0;

        $account = new Account($data);
        $validator = Validator::make($account->toArray(), Account::$rules);
        if ($validator->fails()) {
            return Redirect::route('addaccount')->withErrors($validator)
                ->withInput();
        }
        $result = $account->save();
        if ($result) {
            Session::flash('success', 'The changes has been saved.');
            return Redirect::to(Session::get('previous'));
        } else {
            Session::flash(
                'error',
                'Could not save the new account. Is the account name unique?'
            );

            return Redirect::route('addaccount')->withErrors($validator)
                ->withInput();
        }
    }

    /**
     * Edit an account (the screen)
     *
     * @param Account $account The account.
     *
     * @return \Illuminate\View\View
     */
    public function edit(Account $account)
    {
        Session::put('previous', URL::previous());

        return View::make('accounts.edit')->with(
            'title', 'Edit account ' . $account->name
        )->with('account', $account);
    }

    /**
     * Process the editing of an account.
     *
     * @param Account $account The account.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEdit(Account $account)
    {
        // update the account:
        $account->name = Input::get('name');
        $account->openingbalance = floatval(Input::get('openingbalance'));
        $account->openingbalancedate = Input::get('openingbalancedate');
        $account->hidden = Input::get('hidden') == '1' ? 1 : 0;

        // validate and save:
        $validator = Validator::make($account->toArray(), Account::$rules);
        if ($validator->fails()) {
            return Redirect::route('editaccount', $account->id)->withInput()
                ->withErrors($validator);
        }
        $result = $account->save();

        if ($result) {
            Session::flash('success', 'The account has been updated.');
            return Redirect::to(Session::get('previous'));
        } else {
            Session::flash(
                'error',
                'Could not save the account. Is the account name unique?'
            );
            return Redirect::route('editaccount', $account->id)->withInput()
                ->withErrors($validator);
        }



    }

    /**
     * View to delete an account.
     *
     * @param Account $account the account.
     *
     * @return \Illuminate\View\View|void
     */
    public function delete(Account $account)
    {
        Session::put('previous', URL::previous());

        return View::make('accounts.delete')->with('account', $account)->with(
                'title', 'Delete account ' . $account->name
            );
    }

    /**
     * Deletes an account (really!)
     *
     * @param Account $account The account.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDelete(Account $account)
    {
        $account->delete();
        Session::flash('success', 'Account deleted.');

        return Redirect::to(Session::get('previous'));
    }

    /**
     * @param Account $account The account.
     * @param int     $year    The year
     * @param int     $month   The month
     *
     * @return \Illuminate\View\View|void
     */
    public function showOverview(Account $account, $year = null, $month = null)
    {

        $date = Toolkit::parseDate($year, $month);
        if ($date) {
            $entries = AccountHelper::generateTransactionListByMonth(
                $account, $date
            );
        } else {
            $entries = AccountHelper::generateOverviewOfMonths($account);

        }

        return View::make('accounts.overview')->with('account', $account)->with(
                'title', 'Overview for ' . $account->name
            )->with(
                'transactions', $entries
            )->with('date', $date);
    }

    /**
     * Show the chart overview JSON
     *
     * @param Account $account The account.
     * @param int     $year    The year
     * @param int     $month   The month
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function showChartOverview(
        account $account, $year = null, $month = null
    ) {
        $default = new Carbon;
        $date = Toolkit::parseDate($year, $month, $default);

        if ($date === $default) {
            $period = 2; // two months
            $date->addDays(2);
        } else {
            $period = 1; // one month
            $date->lastOfMonth();
        }

        // make chart
        $chart = App::make('gchart');
        $chart->addColumn('Day', 'date');
        $chart->addColumn('Balance', 'number');
        $chart->addAnnotation(1);
        $chart->addCertainty(1);
        $chart->addInterval(1);
        $chart->addInterval(1);

        $past = clone $date;
        $past->subMonths($period);
        $past->startOfMonth();

        // get transactions with a marker
        $marked = AccountHelper::getMarkedTransactions(
            $account, $past, $date
        );

        $balance = $account->balanceOnDate($past);

        // loop depending on the stuffs
        // interval values
        $intervalAbove = 0;
        $intervalBelow = 0;
        while ($past <= $date) {
            $current = clone $past;
            // do a prediction
            if ($current > $default) {
                $certain = false;
                $data = $account->predictOnDate($current);

                // interval above: the 'max' above from the prediction
                $intervalAbove = ($balance - $data['least']);
                // interval under: the 'min' above from the prediction
                $intervalBelow = ($balance - $data['most']);

                $balance -= $data['prediction'];

                // do just the balance
            } else {
                $certain = true;
                $balance = $account->balanceOnDate($current);
            }

            // find a marker
            $annotation = isset($marked[$current->format('Y-m-d')])
                ? $marked[$current->format('Y-m-d')] : null;

            // add the row.
            $chart->addRow(
                $current, $balance, $annotation[0], $annotation[1], $certain,
                $intervalAbove, $intervalBelow
            );
            $past->addDay();
        }
        $chart->generate();

        return Response::json($chart->getData());
    }
}
