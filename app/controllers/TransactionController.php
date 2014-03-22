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
        }
        // empty by default:
        $prefilled = [
            'description'      => '',
            'amount'           => '',
            'date'             => date('Y-m-d'),
            'account_id'       => null,
            'beneficiary'      => '',
            'category'         => '',
            'budget'           => '',
            'ignoreprediction' => 0,
            'ignoreallowance'  => 0,
            'mark'             => 0
        ];

        // prefill from predictable:
        if(!is_nulL($predictable)) {
            $d = sprintf('%02d', $predictable->dom);
            $prefilled = [
                'description'      => $predictable->description,
                'amount'           => $predictable->amount,
                'date'             => date('Y-m-').$d,
                'account_id'       => null,
                'beneficiary'      => is_null($predictable->beneficiary) ? '' : $predictable->beneficiary->name,
                'category'         => is_null($predictable->category) ? '' : $predictable->category->name,
                'budget'           => is_null($predictable->budget) ? '' : $predictable->budget->name,
                'ignoreprediction' => 0,
                'ignoreallowance'  => 0,
                'mark'             => 0
            ];
        }

        // prefill from old input:
        if (Input::old()) {
            $prefilled = ['description'      => Input::old('description'),
                          'amount'           => floatval(Input::old('amount')),
                          'date'             => Input::old('date'),
                          'account_id'       => intval(Input::old('account_id')),
                          'beneficiary'      => intval(Input::old('beneficiary_id')),
                          'category'         => intval(Input::old('category_id')),
                          'budget'           => intval(Input::old('budget_id')),
                          'ignoreprediction' => intval(Input::old('ignoreprediction')),
                          'ignoreallowance'  => intval(Input::old('ignoreallowance')),
                          'mark'             => intval(Input::old('mark'))

            ];
        }



        $accounts = AccountHelper::accountsAsSelectList();

        return View::make('transactions.add')->with(
            'title', 'Add a transaction'
        )->with('accounts', $accounts)->with('prefilled',$prefilled);
    }

    /**
     * Post process a new Transaction
     *
     * @return View
     */
    public function postAdd(Predictable $p = null)
    {
        Log::debug('AccountID: ' . Input::get('account_id'));
        $account = Auth::user()->accounts()->find(intval(Input::get('account_id')));
        Log::debug('Account is null? '.(is_null($account) ? 1  : 0));
        Log::debug('Test');
        if (is_null($account)) {
            Session::flash('error', 'Invalid account selected.');
            Log::debug('Invalid account (#' . Input::get('account_id') . ')');
            return Redirect::route('addtransaction')->withInput();
            Log::debug('Test2');
        }
        Log::debug('Test3');


        // fields:
        $transaction = new Transaction();

        $transaction->description = Input::get('description');
        $transaction->amount = floatval(Input::get('amount'));
        $transaction->date = Input::get('date');
        $transaction->account()->associate($account);
        /** @noinspection PhpParamsInspection */
        $transaction->user()->associate(Auth::user());
        $transaction->ignoreprediction = is_null(Input::get('ignoreprediction')) ? 0 : 1;
        $transaction->ignoreallowance = is_null(Input::get('ignoreallowance')) ? 0 : 1;
        $transaction->mark = is_null(Input::get('mark')) ? 0 : 1;

        // explode every object at the / and see if there is one.
        // more than one? return to Transaction:
        foreach (['beneficiary', 'category', 'budget'] as $comp) {
            $input = Input::get($comp);
            $parts = explode('/', $input);
            if (count($parts) > 2) {
                Session::flash(
                    'error',
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
            Session::flash('error', 'Could not save transaction.');
            Log::debug('Rule failed: ' . print_r($validator->messages()->all(),true));
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
        $transaction->ignoreprediction = is_null(Input::get('ignoreprediction')) ? 0 : 1;
        $transaction->ignoreallowance = is_null(Input::get('ignoreallowance')) ? 0 : 1;
        $transaction->mark = is_null(Input::get('mark')) ? 0 : 1;

        // explore the components:
        // explode every object at the / and see if there is one.
        // more than one? return to Transaction:
        foreach (['beneficiary', 'category', 'budget'] as $comp) {
            $input = Input::get($comp);
            $parts = explode('/', $input);
            if (count($parts) > 2) {
                Session::flash(
                    'error',
                    'Use forward slashes to indicate parent ' . Str::plural(
                        $comp
                    ) . '. Please don\'t use more than one.'
                );

                return Redirect::route('edittransaction', $transaction->id)->withInput();
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

        // validate and save:
        $validator = Validator::make(
            $transaction->toArray(), Transaction::$rules
        );
        if ($validator->fails()) {
            Session::flash('error', 'The transaction could not be saved.');
            Log::debug('These rules failed: ' . print_r($validator->messages()->all(),true));
            return Redirect::route('edittransaction', $transaction->id)
                ->withInput()->withErrors($validator);
        } else {
            // detach all components first:
            $transaction->components()->sync([]);
            // attach the beneficiary, if it is set:
            /** @var $beneficiary Component */
            $transaction->attachComponent($beneficiary);
            /** @var $budget Component */
            $transaction->attachComponent($budget);
            /** @var $category Component */
            $transaction->attachComponent($category);

            $transaction->save();
            Queue::push('PredictableQueue@processTransaction', $transaction);
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
