<?php
use Carbon\Carbon as Carbon;

/** @noinspection PhpIncludeInspection */
include_once(app_path() . '/helpers/PredictableHelper.php');

/** @noinspection PhpIncludeInspection */
include_once(app_path() . '/helpers/AccountHelper.php');

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
        $predictables = Auth::user()->predictables()->get()->each(
            function ($predictable) {
                $date = new Carbon('2001-01-' . $predictable->dom);
                $predictable->domDisplay = $date->format('jS');
            }
        );


        return View::make('predictables.index')->with('title', 'Predictables')
            ->with('predictables', $predictables);

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
        $date = new Carbon('2001-01-' . $predictable->dom);
        $predictable->domDisplay = $date->format('jS');

        return View::make('predictables.overview')->with(
            'title', 'Overview for ' . $predictable->description
        )->with('predictable', $predictable);
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
        if (!Input::old() && is_null($transaction)) {
            Session::put('previous', URL::previous());
            $prefilled = PredictableHelper::emptyPrefilledAray();
        } else {
            if (!Input::old() && !is_null($transaction)) {
                $prefilled = PredictableHelper::prefilledFromTransaction($transaction);
            } else {
                $prefilled = PredictableHelper::prefilledFromOldInput();
            }
        }
        $title = 'Add a predictable';
        $list = PredictableHelper::componentList();

        return View::make('predictables.add')->with(
            'title', $title
        )->with('components', $list)->with('prefilled', $prefilled);
    }

    /**
     * Post process adding of a predictable.
     *
     * @param Transaction $transaction
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postAdd(Transaction $transaction = null)
    {
        $data = ['description' => Input::get('description'),
                 'dom'         => intval(Input::get('dom')),
                 'pct'         => intval(Input::get('pct')),
                 'inactive'    => Input::get('inactive') == '1' ? 1 : 0,
                 'amount'      => floatval(Input::get('amount'))];

        $predictable = new Predictable($data);
        /** @noinspection PhpParamsInspection */
        $predictable->user()->associate(Auth::user());

        // we use drop downs to select these components:
        $ben = Auth::user()->components()->where('type', 'beneficiary')->find(
            intval(Input::get('beneficiary_id'))
        );
        $bud = Auth::user()->components()->where('type', 'budget')->find(
            intval(Input::get('budget_id'))
        );
        $cat = Auth::user()->components()->where('type', 'category')->find(
            intval(Input::get('category_id'))
        );


        $validator = Validator::make(
            $predictable->toArray(), Predictable::$rules
        );
        if ($validator->fails()) {
            Session::flash(
                'error', 'Could not save the new predictable.'
            );

            return Redirect::route('addpredictable')->withErrors($validator)
                ->withInput()->with('transaction', $transaction);
        }
        $result = $predictable->save();
        if ($result) {

            if (!is_null($bud)) {
                $predictable->components()->save($bud);
            }
            if (!is_null($ben)) {
                $predictable->components()->save($ben);
            }
            if (!is_null($cat)) {
                $predictable->components()->save($cat);
            }

            Session::flash('success', 'The predictable has been saved.');

            return Redirect::to(Session::get('previous'));
        } else {
            Session::flash(
                'error',
                'Could not save the new predictable. Is the predictable name unique?'
            );

            return Redirect::route('addpredictable')->withErrors($validator)
                ->withInput()->with('transaction', $transaction);
        }
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
        $list = PredictableHelper::componentList();

        $accounts = AccountHelper::accountsAsSelectList();

        return View::make('predictables.edit')->with(
            'title', 'Edit predictable "' . $predictable->description . '"'
        )->with('predictable', $predictable)->with('components', $list)->with('prefilled', $prefilled)->with(
                'accounts', $accounts
            );
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

        $predictable->description = Input::get('description');
        $predictable->amount = floatval(Input::get('amount'));
        $predictable->dom = intval(Input::get('dom'));
        $predictable->pct = intval(Input::get('pct'));
        $predictable->account_id = intval(Input::get('account_id'));
        $predictable->inactive = intval(Input::get('inactive')) == 1 ? 1 : 0;


        // we use drop downs to select these components:
        $ben = Auth::user()->components()->where('type', 'beneficiary')->find(
            intval(Input::get('beneficiary_id'))
        );
        $bud = Auth::user()->components()->where('type', 'budget')->find(
            intval(Input::get('budget_id'))
        );
        $cat = Auth::user()->components()->where('type', 'category')->find(
            intval(Input::get('category_id'))
        );

        $validator = Validator::make(
            $predictable->toArray(), Predictable::$rules
        );
        if ($validator->fails()) {
            Session::flash(
                'error', 'Could not save the new predictable.'
            );

            return Redirect::route('editpredictable', $predictable->id)
                ->withErrors($validator)->withInput();
        }
        $result = $predictable->save();
        if ($result) {
            $predictable->components()->detach();
            if (!is_null($bud)) {
                $predictable->components()->save($bud);
            }
            if (!is_null($ben)) {
                $predictable->components()->save($ben);
            }
            if (!is_null($cat)) {
                $predictable->components()->save($cat);
            }

            Session::flash(
                'success', 'The changed predictable has been saved.'
            );

            return Redirect::to(Session::get('previous'));
        } else {
            Session::flash(
                'error',
                'Could not save the changed predictable. Is the predictable name unique?'
            );

            return Redirect::route('editpredictable', $predictable->id)
                ->withErrors($validator)->withInput();
        }


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

        return View::make('predictables.delete')->with(
            'predictable', $predictable
        )->with('title', 'Delete predictable ' . $predictable->description);
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
        Session::flash(
            'success', 'Rescan was queued.'
        );

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
        Session::flash(
            'success', 'Rescan was queued.'
        );

        return Redirect::route('predictableoverview', $predictable->id);
    }
}