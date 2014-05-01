<?php

/**
 * Class PredictableController
 */
class PredictableController extends BaseController
{
    /**
     * Show index.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $predictables = Auth::user()->predictables()->get();

        return View::make('predictables.index')->with('title', 'All predictables')->with('predictables', $predictables);

    }

    /**
     * Overview for predictable.
     *
     * @param Predictable $predictable
     *
     * @return \Illuminate\View\View
     */
    public function overview(Predictable $predictable)
    {
        $transactions = $predictable->transactions()->get();
        return View::make('predictables.overview')->with('transactions', $transactions)->with(
            'predictable', $predictable
        )->with('title', 'Overview for "' . $predictable->description . '"');
    }

    /**
     * Add a new predictable based on a transaction.
     *
     * @param Transaction $transaction
     *
     * @return \Illuminate\View\View
     */
    public function add(Transaction $transaction = null)
    {

        if (Input::old()) {
            $prefilled = PredictableHelper::prefilledFromOldInput();
        } else {
            Session::put('previous', URL::previous());
            if (!is_null($transaction)) {
                $prefilled = PredictableHelper::prefilledFromTransaction($transaction);
            } else {
                $prefilled = PredictableHelper::emptyPrefilledAray();
            }
        }

        $accounts = AccountHelper::accountsAsSelectList();

        return View::make('predictables.add')->with('title', 'Add a new predictable')->with('prefilled', $prefilled)
            ->with('accounts', $accounts);
    }

    /**
     * Post process adding of a predictable.
     *
     * @param Transaction $transaction
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postAdd()
    {
        $account = Auth::user()->accounts()->find(intval(Input::get('account_id')));
        if (is_null($account)) {
            Session::flash('error', 'Invalid account selected.');
            return Redirect::route('addpredictable')->withInput();
        }

        $predictable = new Predictable();
        $predictable->description = Input::get('description');
        $predictable->amount = floatval(Input::get('amount'));
        $predictable->pct = intval(Input::get('pct'));
        $predictable->dom = intval(Input::get('dom'));
        $predictable->inactive = intval(Input::get('inactive'));

        /** @noinspection PhpParamsInspection */
        $predictable->user()->associate(Auth::user());
        $predictable->account()->associate($account);


        $validator = Validator::make($predictable->toArray(), Predictable::$rules);
        if ($validator->fails()) {
            Session::flash('error', 'Could not save predictable.');
            return Redirect::route('addpredictable')->withInput()->withErrors($validator);
        }


        $result = $predictable->save();
        // @codeCoverageIgnoreStart
        if (!$result) {
            Session::flash('error', 'Could not save predictable due to trigger error.');
            return Redirect::route('addpredictable')->withInput()->withErrors($validator);
        }
        // @codeCoverageIgnoreEnd

        // now we can finally add the components:
        // save all components (if any):
        $predictable->saveComponentsFromInput();

        Queue::push('PredictableQueue@scanAll', ['predictable_id' => $predictable->id]);

        Session::flash('success', 'The predictable has been created.');

        return Redirect::to(Session::get('previous'));
    }

    /**
     * Edit predictable.
     *
     * @param Predictable $predictable
     *
     * @return \Illuminate\View\View
     */
    public function edit(Predictable $predictable)
    {
        if (!Input::old()) {
            Session::put('previous', URL::previous());
            $prefilled = PredictableHelper::prefilledFromPredictable($predictable);
        } else {
            $prefilled = PredictableHelper::prefilledFromOldInput();
        }
        $accounts = AccountHelper::accountsAsSelectList();

        return View::make('predictables.edit')->with('predictable', $predictable)->with('accounts', $accounts)->with(
            'title', 'Edit predictable ' . $predictable->description
        )->with('prefilled', $prefilled);
    }

    /**
     * Post edit predictable.
     *
     * @param Predictable $predictable
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEdit(Predictable $predictable)
    {

        $account = Auth::user()->accounts()->find(intval(Input::get('account_id')));
        if (is_null($account)) {
            Session::flash('error', 'Invalid account selected.');
            return Redirect::route('addpredictable')->withInput();
        }

        // update the predictable:
        $predictable->description = Input::get('description');
        $predictable->amount = floatval(Input::get('amount'));
        $predictable->dom = intval(Input::get('dom'));
        $predictable->inactive = is_null(Input::get('inactive')) ? 0 : 1;
        $predictable->account_id = intval(Input::get('account_id'));
        $predictable->pct = intval(Input::get('pct'));


        // validate and save:
        $validator = Validator::make(
            $predictable->toArray(), Predictable::$rules
        );

        if ($validator->fails()) {
            Session::flash('error', 'The predictable could not be saved.');
            return Redirect::route('editpredictable', $predictable->id)
                ->withInput()->withErrors($validator);
        }

        // try another save.
        $result = $predictable->save();
        // @codeCoverageIgnoreStart
        if (!$result) {
            Session::flash('error', 'The predictable could not be saved.');
            return Redirect::route('editpredictable', $predictable->id)->withInput()->withErrors($validator);
        }
        // @codeCoverageIgnoreEnd

        // now add or update the components from the input:
        $predictable->saveComponentsFromInput();

        Cache::userFlush();

        Queue::push('PredictableQueue@scan', ['predictable_id' => $predictable->id]);
        Session::flash('success', 'The predictable has been saved.');

        return Redirect::to(Session::get('previous'));


    }

    /**
     * Shows the view to delete a certain predictable.
     *
     * @param Predictable $predictable The predictable
     *
     * @return \Illuminate\View\View
     */
    public function delete(Predictable $predictable)
    {
        if (!Input::old()) {
            Session::put('previous', URL::previous());
        }

        return View::make('predictables.delete')->with('predictable', $predictable)->with(
            'title', 'Delete predictable ' . $predictable->description
        );
    }

    /**
     * Process the actual deleting.
     *
     * @param Predictable $predictable The predictable
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDelete(Predictable $predictable)
    {
        $predictable->delete();
        Session::flash('success', 'Predictable deleted.');

        return Redirect::to(Session::get('previous'));
    }

    /**
     * @param Predictable $predictable
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rescan(Predictable $predictable)
    {
        Queue::push('PredictableQueue@scan', ['predictable_id' => $predictable->id]);
        Session::flash('success', 'Rescan was queued.');

        return Redirect::route('predictableoverview', $predictable->id);
    }

    /**
     * @param Predictable $predictable
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rescanAll(Predictable $predictable)
    {
        Queue::push('PredictableQueue@scanAll', ['predictable_id' => $predictable->id]);
        Session::flash('success', 'Rescan was queued.');

        return Redirect::route('predictableoverview', $predictable->id);
    }
}