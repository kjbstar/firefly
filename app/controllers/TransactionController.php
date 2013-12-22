<?php
/**
 * File contains the TransactionController
 *
 * PHP version 5.5.6
 *
 * @category Controllers
 * @package  Controllers
 * @author   Sander Dorigo <sander@dorigo.nl>
 * @license  GPL 3.0
 * @link     http://geld.nder.dev/
 */

/**
 * Class TransactionController
 *
 * @category AppControllers
 * @package  AppControllers
 * @author   Sander dorigo <sander@dorigo.nl>
 * @license  GPL 3.0
 * @link     http://geld.nder.dev/
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
            ->orderBy('id', 'DESC')->paginate(50);
        $query = Input::get('search') ? Input::get('search') : null;

        return View::make('transactions.index')->with(
            'title', 'All transactions'
        )->with('transactions', $transactions)->with('query', $query);
    }

    /**
     * Add a new transaction
     *
     * @param Account $account The account to put it in.
     *
     * @return View
     */
    public function add(Account $account = null)
    {
        $accounts = [];
        foreach (Auth::user()->accounts()->where('hidden', 0)->get() as $a) {
            $accounts[$a->id] = $a->name;
        }
        Session::put('previous', URL::previous());

        return View::make('transactions.add')->with(
            'title', 'Add a transaction'
        )->with('account', $account)->with('accounts', $accounts)->with(
                'id', $account ? $account->id : null
            );
    }

    /**
     * Post process a new Transaction
     *
     * @return View
     */
    public function postAdd()
    {
        $data = [];
        $data['description'] = Input::get('description');
        $data['amount'] = Input::get('amount');
        $data['date'] = Input::get('date');
        $data['account_id'] = Input::get('account_id');
        $data['user_id'] = Auth::user()->id;
        $data['ignore'] = Input::get('ignore') == '1' ? 1 : 0;
        $data['mark'] = Input::get('mark') == '1' ? 1 : 0;
        $transaction = new Transaction($data);

        // save and / or create the beneficiary:

        $ben = Component::findOrCreate(
            'beneficiary', Input::get('beneficiary')
        );
        $bud = Component::findOrCreate('budget', Input::get('budget'));
        $cat = Component::findOrCreate(
            'category', Input::get('category')
        );

        $validator = Validator::make(
            $transaction->toArray(), Transaction::$rules
        );
        if ($validator->fails()) {
            return Redirect::route('addtransaction')->withInput()->withErrors(
                    $validator
                );
        } else {
            $transaction->save();

            $transaction->addComponent($ben);
            $transaction->addComponent($bud);
            $transaction->addComponent($cat);
            Session::flash('success', 'The transaction has been created.');

            return Redirect::to(Session::get('previous'));
        }
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
        $accounts = [];
        foreach (Auth::user()->accounts()->where('hidden', 0)->get() as $a) {
            $accounts[$a->id] = $a->name;
        }

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
        $transaction->ignore = Input::get('ignore') == '1' ? 1 : 0;
        $transaction->mark = Input::get('mark') == '1' ? 1 : 0;

        // beneficiary and budget:
        $ben = Component::findOrCreate(
            'beneficiary', Input::get('beneficiary')
        );
        $bud = Component::findOrCreate('budget', Input::get('budget'));
        $cat = Component::findOrCreate(
            'category', Input::get('category')
        );
        $transaction->components()->detach();
        // attach the beneficiary, if it is set:
        if (!is_null($ben)) {
            $transaction->components()->attach($ben->id);
        }
        if (!is_null($bud)) {
            $transaction->components()->attach($bud->id);
        }
        if (!is_null($cat)) {
            $transaction->components()->attach($cat->id);
        }

        // validate and save:
        $validator = Validator::make(
            $transaction->toArray(), Transaction::$rules
        );
        if ($validator->fails()) {
            return Redirect::route('edittransaction')->withInput()->withErrors(
                    $validator
                );
        } else {
            $transaction->save();
            Session::flash('success', 'The transaction has been saved.');

            return Redirect::route('transactions');
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
        return View::make('transactions.delete')->with(
            'transaction', $transaction
        )->with('title', 'Delete transaction ' . $transaction->title);
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

        return Redirect::route('transactions');
    }

}
