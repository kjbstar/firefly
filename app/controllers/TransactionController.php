<?php
/** @noinspection PhpIncludeInspection */
include_once(app_path() . '/helpers/AccountHelper.php');
/** @noinspection PhpIncludeInspection */
include_once(app_path() . '/helpers/TransactionHelper.php');
/** @noinspection PhpIncludeInspection */
include_once(app_path() . '/helpers/ComponentHelper.php');

/**
 * Class TransactionController
 */
class TransactionController extends BaseController
{

    /**
     * Shows the index with all transactions.
     *
     * @return View
     */
    public function showIndex()
    {
        $transactions = Auth::user()->transactions()->orderBy('date', 'DESC')->orderBy('id', 'DESC')->paginate(25);
        return View::make('transactions.index')->with('title', 'All transactions')->with('transactions', $transactions);
    }

    /**
     * Add a new transaction.
     *
     * @param Predictable $predictable
     *
     * @return \Illuminate\View\View
     */
    public function add(Predictable $predictable = null)
    {
        if (!Input::old()) {
            Session::put('previous', URL::previous());
            $prefilled = TransactionHelper::emptyPrefilledAray();
        } else {
            $prefilled = TransactionHelper::prefilledFromOldInput();
        }
        // prefill from predictable:
        if (!is_null($predictable)) {
            $prefilled = TransactionHelper::prefilledFromPredictable($predictable);
        }


        $accounts = AccountHelper::accountsAsSelectList();

        return View::make('transactions.add')->with(
            'title', 'Add a transaction'
        )->with('accounts', $accounts)->with('prefilled', $prefilled);
    }

    /**
     * Post process a new Transaction
     *
     * @return View
     */
    public function postAdd()
    {
        $account = Auth::user()->accounts()->find(intval(Input::get('account_id')));
        if (is_null($account)) {
            Session::flash('error', 'Invalid account selected.');
            return Redirect::route('addtransaction')->withInput();
        }

        $transaction = new Transaction();
        $transaction->description = Input::get('description');
        $transaction->amount = floatval(Input::get('amount'));
        $transaction->date = Input::get('date');
        $transaction->account()->associate($account);

        /** @noinspection PhpParamsInspection */
        $transaction->user()->associate(Auth::user());
        $transaction->ignoreprediction = intval(Input::get('ignoreprediction'));
        $transaction->ignoreallowance = intval(Input::get('ignoreallowance'));
        $transaction->mark = intval(Input::get('mark'));

        // explode every object at the / and see if there is one.
        // more than one? return to Transaction:
        $beneficiary = ComponentHelper::saveComponentFromText('beneficiary', Input::get('beneficiary'));
        $category = ComponentHelper::saveComponentFromText('category', Input::get('category'));
        $budget = ComponentHelper::saveComponentFromText('budget', Input::get('budget'));


        // save and / or create the beneficiary:
        $validator = Validator::make($transaction->toArray(), Transaction::$rules);
        if ($validator->fails()) {
            Session::flash('error', 'Could not save transaction.');
            return Redirect::route('addtransaction')->withInput()->withErrors($validator);
        }

        $result = $transaction->save();
        if (!$result) {
            Session::flash('error', 'Could not save transaction.');
            return Redirect::route('addtransaction')->withInput()->withErrors($validator);
        }

        // attach the beneficiary, if it is set:
        $transaction->attachComponent($beneficiary);
        $transaction->attachComponent($budget);
        $transaction->attachComponent($category);

        Queue::push('PredictableQueue@processTransaction', ['transaction_id' => $transaction->id]);

        Session::flash('success', 'The transaction has been created.');

        return Redirect::to(Session::get('previous'));
    }

    /**
     * Show the view to edit the given transaction.
     *
     * @param Transaction $transaction The transaction.
     *
     * @return \Illuminate\View\View
     */
    public function edit(Transaction $transaction)
    {
        if (!Input::old()) {
            Session::put('previous', URL::previous());
            $prefilled = TransactionHelper::prefilledFromTransaction($transaction);
        } else {
            $prefilled = TransactionHelper::prefilledFromOldInput();
        }
        $accounts = AccountHelper::accountsAsSelectList();

        return View::make('transactions.edit')->with('transaction', $transaction)->with('accounts', $accounts)->with(
            'title', 'Edit transaction ' . $transaction->description
        )->with('prefilled', $prefilled);
    }

    /**
     * Process the changes to the transaction (and validate)
     *
     * @param Transaction $transaction The transaction
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function postEdit(Transaction $transaction)
    {

        // update the transaction:
        $transaction->description = Input::get('description');
        $transaction->amount = floatval(Input::get('amount'));
        $transaction->date = Input::get('date');
        $transaction->account_id = intval(Input::get('account_id'));
        $transaction->ignoreprediction = is_null(Input::get('ignoreprediction')) ? 0 : 1;
        $transaction->ignoreallowance = is_null(Input::get('ignoreallowance')) ? 0 : 1;
        $transaction->mark = is_null(Input::get('mark')) ? 0 : 1;

        // explode every object at the / and see if there is one.
        // more than one? return to Transaction:
        $beneficiary = ComponentHelper::saveComponentFromText('beneficiary', Input::get('beneficiary'));
        $category = ComponentHelper::saveComponentFromText('category', Input::get('category'));
        $budget = ComponentHelper::saveComponentFromText('budget', Input::get('budget'));

        // validate and save:
        $validator = Validator::make(
            $transaction->toArray(), Transaction::$rules
        );
        if ($validator->fails()) {
            Session::flash('error', 'The transaction could not be saved.');
            Log::debug('These rules failed: ' . print_r($validator->messages()->all(), true));
            return Redirect::route('edittransaction', $transaction->id)
                ->withInput()->withErrors($validator);
        } else {
            // detach all components first:
            $transaction->components()->sync([]);
            // attach the beneficiary, if it is set:
            $transaction->attachComponent($beneficiary);
            $transaction->attachComponent($budget);
            $transaction->attachComponent($category);

            $result = $transaction->save();
            if (!$result) {
                Session::flash('error', 'The transaction could not be saved.');
                Log::debug('These rules failed: ' . print_r($validator->messages()->all(), true));
                return Redirect::route('edittransaction', $transaction->id)
                    ->withInput()->withErrors($validator);
            }
            Queue::push('PredictableQueue@processTransaction', ['transaction_id' => $transaction->id]);
            Session::flash('success', 'The transaction has been saved.');

            return Redirect::to(Session::get('previous'));
        }
    }

    /**
     * Shows the view to delete a certain transaction.
     *
     * @param Transaction $transaction The transaction
     *
     * @return \Illuminate\View\View
     */
    public function delete(Transaction $transaction)
    {
        if (!Input::old()) {
            Session::put('previous', URL::previous());
        }

        return View::make('transactions.delete')->with(
            'transaction', $transaction
        )->with('title', 'Delete transaction ' . $transaction->description);
    }

    /**
     * Process the actual deleting.
     *
     * @param Transaction $transaction The transaction
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDelete(Transaction $transaction)
    {
        $transaction->delete();
        Session::flash('success', 'Transaction deleted.');

        return Redirect::to(Session::get('previous'));
    }

}
