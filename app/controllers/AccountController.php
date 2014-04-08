<?php
/** @noinspection PhpIncludeInspection */
require_once(app_path() . '/helpers/AccountHelper.php');
/** @noinspection PhpIncludeInspection */
require_once(app_path() . '/helpers/Toolkit.php');
use Carbon\Carbon as Carbon;

/**
 * Class AccountController
 */
class AccountController extends BaseController
{

    /**
     * Shows the index page.
     *
     * @return View
     */
    public function showIndex()
    {
        // get the accounts:
        $accounts = Auth::user()->accounts()->get();
        // get their id's:
        $ids = [];
        foreach ($accounts as $account) {
            $ids[] = $account->id;
        }
        // get balances:
        $date = new Carbon;
        $raw = Balancemodifier::where('date', '<=', $date->format('Y-m-d'))->whereIn('account_id', $ids)->groupBy(
            'account_id'
        )->get(['account_id', DB::Raw('SUM(`balance`) as aggregate')]);
        $balances = [];
        foreach ($raw as $entry) {
            /** @noinspection PhpUndefinedFieldInspection */
            $balances[$entry->account_id] = floatval($entry->aggregate);
        }
        return View::make('accounts.index')->with('accounts', $accounts)->with('balances', $balances)->with(
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
        if (!Input::old()) {
            Session::put('previous', URL::previous());
            $prefilled = AccountHelper::emptyPrefilledAray();
        } else {
            $prefilled = AccountHelper::prefilledFromOldInput();
        }

        return View::make('accounts.add')->with('title', 'Add a new account')->with('prefilled', $prefilled);
    }

    /**
     * Post process the addition of the Account.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postAdd()
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $data = [
            'name'               => Input::get('name'),
            'openingbalance'     => floatval(Input::get('openingbalance')),
            'currentbalance'     => floatval(Input::get('openingbalance')),
            'openingbalancedate' => Input::get('openingbalancedate'),
            'hidden'             => Input::get('hidden') == '1' ? 1 : 0,
            'shared'             => Input::get('shared') == '1' ? 1 : 0,
            'user_id'            => Auth::user()->id
        ];
        // create the new account:
        $account = new Account($data);

        // validate it:
        $validator = Validator::make($account->toArray(), Account::$rules);

        // validation failed!
        if ($validator->fails()) {
            Session::flash('error', 'Validation failed. Please try harder.');
            return Redirect::route('addaccount')->withErrors($validator)->withInput();
        }

        // try to save it:
        $result = $account->save();

        // it failed again!
        if (!$result) {
            Session::flash('error', 'Could not save the new account. Is the account name unique?');
            return Redirect::route('addaccount')->withErrors($validator)->withInput();

        }

        // success!
        Session::flash('success', 'The changes has been saved.');
        return Redirect::to(Session::get('previous'));
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
        if (!Input::old()) {
            Session::put('previous', URL::previous());
        }
        $prefilled = AccountHelper::prefilledFromAccount($account);
        if (Input::old()) {
            $prefilled = AccountHelper::prefilledFromOldInput();
        }
        return View::make('accounts.edit')->with('title', 'Edit account "' . $account->name . '"')->with(
            'account', $account
        )
            ->with('prefilled', $prefilled);
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
        $account->shared = Input::get('shared') == '1' ? 1 : 0;

        // validate it:
        $validator = Validator::make($account->toArray(), Account::$rules);

        // failed!
        if ($validator->fails()) {
            Session::flash('error', 'Could not save the account.');
            return Redirect::route('editaccount', $account->id)->withInput()->withErrors($validator);
        }

        // try to save it
        $result = $account->save();

        // failed again!
        if (!$result) {
            Session::flash('error', 'Could not save the account. Is the account name unique?');
            return Redirect::route('editaccount', $account->id)->withInput()->withErrors($validator);

        }
        // success!
        Session::flash('success', 'The account has been updated.');
        return Redirect::to(Session::get('previous'));


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
        if (!Input::old()) {
            Session::put('previous', URL::previous());
        }

        return View::make('accounts.delete')->with('account', $account)->with(
            'title', 'Delete account "' . $account->name.'"'
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
     * Shows an overview of an account.
     *
     * @param Account $account
     *
     * @return \Illuminate\View\View
     */
    public function showOverview(Account $account)
    {
        $months = AccountHelper::months($account);
        $title = 'Overview for account "' . $account->name . '"';
        return View::make('accounts.overview')->with('account', $account)->with('title', $title)->with(
            'months', $months
        );
    }

    /**
     * Shows the chart that goes with prev.
     *
     * @param Account $account
     *
     * @return mixed
     */
    public function showOverviewChart(Account $account)
    {
        $months = AccountHelper::months($account);
        $chart = App::make('gchart');
        $chart->addColumn('date', 'date');
        $chart->addColumn('Balance for "' . $account->name . '"', 'number');
        foreach ($months as $month) {
            $chart->addRow($month['date'], $month['balance']);
        }
        $chart->generate();

        // catch debug request:
        if (Input::get('debug') == 'true') {
            return '<pre>' . print_r($chart->getData(), true) . '</pre>';
        } else {
            return $chart->getData();
        }
    }

    /**
     * Same for a specific month.
     *
     * @param Account $account
     * @param         $year
     * @param         $month
     *
     * @return \Illuminate\View\View
     */
    public function showOverviewByMonth(Account $account, $year, $month)
    {
        $date = Toolkit::parseDate($year, $month);
        $mutations = AccountHelper::mutations($account, $date);
        $title = 'Overview for account "' . $account->name . '" in ' . $date->format('F Y');
        return View::make('accounts.overview-by-month')->with('account', $account)->with('title', $title)->with(
            'mutations', $mutations
        )->with('date', $date);

    }

    /**
     * Chart for account and month
     *
     * @param Account $account
     * @param         $year
     * @param         $month
     *
     * @return mixed
     */
    public function showOverviewChartByMonth(Account $account, $year, $month)
    {
        $date = Toolkit::parseDate($year, $month);
        $date->startOfMonth();
        $end = clone $date;
        $end->endOfMonth();

        // all columns:
        $chart = App::make('gchart');
        $chart->addColumn('date', 'date');
        $chart->addColumn('Balance for "' . $account->name . '"', 'number');
        $chart->addAnnotation(1);
        $chart->addCertainty(1);
        $chart->addInterval(1);
        $chart->addInterval(1);
        $chart->addInterval(1);
        $chart->addInterval(1);

        // all annotations:
        $marked = AccountHelper::getMarkedTransactions($account, $date, $end);

        // first balance:

        $now = new Carbon;
        if ($now < $date) {
            $balance = $account->balanceOnDate($date);
            $above = $balance;
            $below = $balance;
            $alt1 = $balance;
            $alt2 = $balance;
        }


        while ($date <= $end) {
            $current = clone $date;
            if ($current < $now) {
                // get the past:
                $certain = true;
                $balance = $account->balanceOnDate($current);
                $above = $balance;
                $below = $balance;
                $alt1 = $balance;
                $alt2 = $balance;
            } else {
                // predict the future:
                $certain = false;
                $prediction = $account->predictOnDate($current);
                /** @noinspection PhpUndefinedVariableInspection */
                $above -= $prediction['least'];
                /** @noinspection PhpUndefinedVariableInspection */
                $below -= $prediction['most'];

                $alt1 -= $prediction['prediction_alt1'];

                $alt2 -= $prediction['prediction_alt2'];

                /** @noinspection PhpUndefinedVariableInspection */
                $balance -= $prediction['prediction'];
            }
            // get the marked transactions:
            $annotation = isset($marked[$current->format('Y-m-d')]) ? $marked[$current->format('Y-m-d')] : null;
            $chart->addRow($current, $balance, $annotation[0], $annotation[1], $certain, $above, $below, $alt1, $alt2);
            $date->addDay();
        }

        $chart->generate();

        if (Input::get('debug') == 'true') {
            return '<pre>' . print_r($chart->getData(), true) . '</pre>';
        } else {
            return Response::json($chart->getData());
        }

    }

}
