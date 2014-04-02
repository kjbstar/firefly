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

        return View::make('accounts.add')->with(
            'title', 'Add account'
        )->with('prefilled', $prefilled);
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
        $data['hidden'] = Input::get('hidden') == '1' ? 1 : 0;
        $data['shared'] = Input::get('shared') == '1' ? 1 : 0;

        $account = new Account($data);
        /** @noinspection PhpParamsInspection */
        $account->user()->associate(Auth::user());
        $validator = Validator::make($account->toArray(), Account::$rules);
        if ($validator->fails()) {
            Session::flash(
                'error',
                'Validation failed. Please try harder.'
            );
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
        if (!Input::old()) {
            Session::put('previous', URL::previous());
        }
        $prefilled = AccountHelper::prefilledFromAccount($account);
        if (Input::old()) {
            $prefilled = AccountHelper::prefilledFromOldInput();
        }


        return View::make('accounts.edit')->with(
            'title', 'Edit account ' . $account->name
        )->with('account', $account)->with('prefilled', $prefilled);
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
            Session::flash(
                'error',
                'Could not save the account.'
            );
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
        if (!Input::old()) {
            Session::put('previous', URL::previous());
        }

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
        $transfers = [];
        if ($date) {
            $entries = AccountHelper::generateTransactionListByMonth(
                $account, $date
            );

            // also grab a list of transfers by month:
            $transfers = AccountHelper::generateTransferListByMonth(
                $account, $date
            );

            $title = 'Overview for ' . $account->name . ' in ' . $date->format(
                    'F Y'
                );
        } else {
            $entries = AccountHelper::generateOverviewOfMonths($account);
            $title = 'Overview for ' . $account->name;
        }


        return View::make('accounts.overview')->with('account', $account)->with(
            'title', $title
        )->with(
                'transactions', $entries
            )->with('date', $date)->with('transfers', $transfers);
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
        $date = $date->endOfMonth();
        // make chart
        $chart = App::make('gchart');
        $chart->addColumn('Day', 'date');
        $chart->addColumn('Balance', 'number');
        $chart->addAnnotation(1);
        $chart->addCertainty(1);
        $chart->addInterval(1);
        $chart->addInterval(1);

        $past = clone $date;
        $past->startOfMonth();

        // get transactions with a marker
        $marked = AccountHelper::getMarkedTransactions(
            $account, $past, $date
        );

        $balance = $account->balanceOnDate($past);

        // loop depending on the stuffs
        // interval values
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
                $intervalAbove = $balance;
                $intervalBelow = $balance;
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
        if (Input::get('debug') == 'true') {
            return '<pre>' . print_r($chart->getData(), true) . '</pre>';
        }

        return Response::json($chart->getData());
    }

    public function showChartAllOverview($year, $month)
    {
        $start = Toolkit::parseDate($year, $month);
        $end = clone $start;
        $end->endOfMonth();
        $realDay = new Carbon;

        // make chart
        $chart = App::make('gchart');
        $chart->addColumn('Day', 'date');

        $marked = [];
        $balances = [];
        $accounts = Auth::user()->accounts()->notHidden()->where(
            'openingbalancedate', '<=', $start->format('Y-m-d')
        )->get();
        foreach ($accounts as $account) {
            $x = $chart->addColumn($account->name . ' Balance', 'number');
            $chart->addAnnotation($x);
            $marked[$account->id] = AccountHelper::getMarkedTransactions(
                $account, $start, $end
            );
            $balances[$account->id] = $account->balanceOnDate($start);
        }
        // loop again for data:
        $current = clone $start;

        while ($current <= $end) {
            $row = [];
            $row[] = clone $current;
            if ($current <= $realDay) {

                foreach ($accounts as $account) {
                    $balances[$account->id] = $account->balanceOnDate($current);
                    $annotation = isset($marked[$account->id][$current->format(
                        'Y-m-d'
                    )]) ? $marked[$account->id][$current->format('Y-m-d')]
                        : null;
                    // add to row:
                    $row[] = $balances[$account->id];
                    $row[] = $annotation[0];
                    $row[] = $annotation[1];

                }
            } else {
                $row[] = null;
                $row[] = null;
                $row[] = null;
            }
            $chart->addRowArray($row);


            $current->addDay();
        }
        $chart->generate();

        return Response::json($chart->getData());

    }
}
