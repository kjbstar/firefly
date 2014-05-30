<?php
use Carbon\Carbon as Carbon;
use Firefly\Helper\Account\AccountHelperInterface as AHI;
use Firefly\Storage\Account\AccountRepositoryInterface as ARI;

/**
 * Class AccountController
 */
class AccountController extends BaseController
{
    public function __construct(ARI $accounts, AHI $helper)
    {
        $this->accounts = $accounts;
        $this->helper = $helper;
    }

    /**
     * Shows the index page.
     *
     * @return View
     */
    public function index()
    {
        // get the accounts:
        $accounts = $this->accounts->all();
        return View::make('accounts.index')->with('accounts', $accounts)->with('title', 'All accounts');
    }

    /**
     * Shows the view to add a new account.
     *
     * @return \Illuminate\View\View
     */
    public function add()
    {
        return View::make('accounts.add')->with('title', 'Add a new account');
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
            'inactive'           => Input::get('inactive') == '1' ? 1 : 0,
            'shared'             => Input::get('shared') == '1' ? 1 : 0,
            'user_id'            => Auth::user()->id
        ];
        // create the new account:

        /** @var $account Account */
        $account = $this->accounts->initialize($data);

        if(!$account->validate()) {
            Session::flash('error', 'Validation failed. Please try harder.');
            Session::flash('error_extended', $account->errors()->first());
            return Redirect::route('addaccount')->withErrors($account->validator())->withInput();
        }
        // save it
        $account->save();
        // success!
        Cache::userFlush();
        Session::flash('success', 'The new account has been created.');
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
        return View::make('accounts.edit')->with('title', 'Edit account "' . $account->name . '"')->with('account', $account);
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
        $account->inactive = Input::get('inactive') == '1' ? 1 : 0;
        $account->shared = Input::get('shared') == '1' ? 1 : 0;


        if(!$account->validate()) {
            Session::flash('error', 'Could not save the account.');
            Session::flash('error_extended', $account->errors()->first());
            return Redirect::route('editaccount', $account->id)->withInput()->withErrors($account->validator());
        }
        $account->save();

        // success!
        Cache::userFlush();
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
        Session::put('previous', URL::previous());


        return View::make('accounts.delete')->with('account', $account)->with(
            'title', 'Delete account "' . $account->name . '"'
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
    public function overview(Account $account)
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
    public function overviewChart(Account $account)
    {
        $months = AccountHelper::months($account);
        $chart = App::make('gchart');
        $chart->addColumn('date', 'date');
        $chart->addColumn('Balance', 'number');
        foreach ($months as $month) {
            $chart->addRow($month['date'], $month['balance']);
        }
        $chart->generate();

        // catch debug request:
        if (Input::get('debug') == 'true') {
            // @codeCoverageIgnoreStart
            return '<pre>' . print_r($chart->getData(), true) . '</pre>';
            // @codeCoverageIgnoreEnd
        } else {
            return Response::json($chart->getData());
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
    public function overviewByMonth(Account $account, $year, $month)
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
    public function overviewChartByMonth(Account $account, $year, $month)
    {
        $date = Toolkit::parseDate($year, $month);
        $date->startOfMonth();
        $end = clone $date;
        $end->endOfMonth();

        // all columns:
        $chart = App::make('gchart');
        $chart->addColumn('date', 'date');
        $chart->addColumn('Balance', 'number');
        $chart->addAnnotation(1);
        $chart->addCertainty(1);
        $chart->addInterval(1);
        $chart->addInterval(1);

        // all annotations:
        $marked = AccountHelper::getMarkedTransactions($account, $date, $end);

        // first balance:

        $now = new Carbon;
        if ($now < $date) {
            $balance = $this->helper->balanceOnDate($account, $date);
            $above = $balance;
            $below = $balance;
        }


        while ($date <= $end) {
            $current = clone $date;
            if ($current < $now) {
                // get the past:
                $certain = true;
                $balance = $this->helper->balanceOnDate($account, $date);
                $above = $balance;
                $below = $balance;
            } else {
                // predict the future:
                $certain = false;
                $prediction = $this->helper->predictOnDate($account, $date);

                /** @noinspection PhpUndefinedVariableInspection */
                $above -= $prediction['least'];

                /** @noinspection PhpUndefinedVariableInspection */
                $below -= $prediction['most'];

                /** @noinspection PhpUndefinedVariableInspection */
                $balance -= $prediction['prediction'];
            }
            // get the marked transactions:
            $annotation = isset($marked[$current->format('Y-m-d')]) ? $marked[$current->format('Y-m-d')] : null;
            $chart->addRow($current, $balance, $annotation[0], $annotation[1], $certain, $above, $below);
            $date->addDay();
        }

        $chart->generate();

        if (Input::get('debug') == 'true') {
            // @codeCoverageIgnoreStart
            return '<pre>' . print_r($chart->getData(), true) . '</pre>';
            // @codeCoverageIgnoreEnd
        } else {
            return Response::json($chart->getData());
        }
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
    public function predict(Account $account, $year, $month, $day)
    {
        $date = Carbon::createFromDate($year, $month, $day);
        $basicPrediction = $this->helper->predictOnDate($account, $date);
        $information = $this->helper->predictionInformation($account, $date);

        return View::make('accounts.predict')->with('prediction', $basicPrediction)->with('date', $date)->with(
            'information', $information
        );
    }

}
