<?php
/** @noinspection PhpIncludeInspection */
include_once(app_path() . '/helpers/AccountHelper.php');

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
        $transactions = Auth::user()->transactions()->orderBy('date', 'DESC')
            ->orderBy('id', 'DESC')->paginate(25);


        return View::make('transactions.index')->with(
            'title', 'All transactions'
        )->with('transactions', $transactions);
    }

    /**
     * Add a new transaction
     *
     * @return View
     */
    public function add(Predictable $predictable = null)
    {
        if (!Input::old()) {
            Session::put('previous', URL::previous());
        }

        $accounts = AccountHelper::accountsAsSelectList();

        return View::make('transactions.add')->with(
            'title', 'Add a transaction'
        )->with('accounts', $accounts)->with('predictable', $predictable);
    }

    /**
     * Post process a new Transaction
     *
     * @return View
     */
    public function postAdd()
    {
        $account = Auth::user()->accounts()->find(
            intval(Input::get('account_id'))
        );
        if (is_null($account)) {
            Session::flash('warning', 'Invalid account selected.');

            return Redirect::route('addtransaction')->withInput();
        }


        // fields:
        $transaction = new Transaction();

        $transaction->description = Input::get('description');
        $transaction->amount = floatval(Input::get('amount'));
        $transaction->date = Input::get('date');
        $transaction->account()->associate($account);
        /** @noinspection PhpParamsInspection */
        $transaction->user()->associate(Auth::user());
        $transaction->ignoreprediction = Input::get('ignoreprediction');
        $transaction->ignoreallowance = Input::get('ignoreallowance');
        $transaction->mark = Input::get('mark');

        // explode every object at the / and see if there is one.
        // more than one? return to Transaction:
        foreach (['beneficiary', 'category', 'budget'] as $comp) {
            $input = Input::get($comp);
            $parts = explode('/', $input);
            if (count($parts) > 2) {
                Session::flash(
                    'warning',
                    'Use forward slashes to indicate parent ' . Str::plural(
                        $comp
                    ) . '. Please don\'t use more than one.'
                );

                return Redirect::route('addtransaction')->withInput();
            }
            // count is one? that's the object!
            if (count($parts) == 1) {
                $$comp = Component::findOrCreate($comp, Input::get($comp));
            }
            // count is two? parent + child.
            if (count($parts) == 2) {
                $parent = Component::findOrCreate($comp, $parts[0]);
                $$comp = Component::findOrCreate($comp, $parts[1]);
                $$comp->parent_component_id = $parent->id;
                $$comp->save();

            }
        }

        // save and / or create the beneficiary:
        $validator = Validator::make(
            $transaction->toArray(), Transaction::$rules
        );
        if ($validator->fails()) {
            return Redirect::route('addtransaction')->withInput()->withErrors(
                $validator
            );
        }
        $transaction->save();

        // attach the beneficiary, if it is set:
        /** @var $beneficiary Component */
        $transaction->attachComponent($beneficiary);
        /** @var $budget Component */
        $transaction->attachComponent($budget);
        /** @var $category Component */
        $transaction->attachComponent($category);
        Log::debug('save');
        Queue::push('PredictableQueue@processTransaction', $transaction);
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
        }
        $accounts = AccountHelper::accountsAsSelectList();

        return View::make('transactions.edit')->with(
            'transaction', $transaction
        )->with('accounts', $accounts)->with(
                'title', 'Edit transaction ' . $transaction->description
            );
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
        $transaction->ignoreprediction = Input::get('ignoreprediction');
        $transaction->ignoreallowance = Input::get('ignoreallowance');
        $transaction->mark = Input::get('mark');

        // beneficiary and budget:

        $ben = Component::findOrCreate(
            'beneficiary', Input::get('beneficiary')
        );
        $bud = Component::findOrCreate('budget', Input::get('budget'));
        $cat = Component::findOrCreate('category', Input::get('category'));
        $transaction->components()->detach();
        // attach the beneficiary, if it is set:
        $transaction->attachComponent($ben);
        $transaction->attachComponent($bud);
        $transaction->attachComponent($cat);

        // validate and save:
        $validator = Validator::make(
            $transaction->toArray(), Transaction::$rules
        );
        if ($validator->fails()) {
            return Redirect::route('edittransaction', $transaction->id)
                ->withInput()->withErrors($validator);
        } else {
            $transaction->save();
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
