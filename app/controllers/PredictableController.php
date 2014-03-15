<?php
use Carbon\Carbon as Carbon;

/** @noinspection PhpIncludeInspection */
include_once(app_path() . '/helpers/PredictableHelper.php');

class PredictableController extends BaseController
{
    public function index()
    {
        $predictables = Auth::user()->predictables()->get()->each(
            function ($predictable) {
                $date = new Carbon('2001-01-' . $predictable->dom);
                $predictable->dom_display = $date->format('jS');
            }
        );


        return View::make('predictables.index')->with('title', 'Predictables')
            ->with('predictables', $predictables);

    }

    public function overview(Predictable $predictable)
    {
        $date = new Carbon('2001-01-' . $predictable->dom);
        $predictable->dom_display = $date->format('jS');

        return View::make('predictables.overview')->with(
            'title', 'Overview for ' . $predictable->description
        )->with('predictable', $predictable);
    }

    public function add(Transaction $transaction = null)
    {
        if (!Input::old()) {
            Session::put('previous', URL::previous());
        }
        // do something easy for the prefilled values so the
        // view will be easier to manage:
        $title = 'Add a predictable';

        $prefilled = ['description' => '', 'amount' => 0, 'leeway' => 10,
                      'dom'         => 1, 'beneficiary' => 0, 'category' => 0,
                      'budget'      => 0, 'inactive' => false];

        if (!is_null($transaction)) {
            $prefilled = ['description' => $transaction->description,
                          'amount' => floatval(
                              $transaction->amount
                          ), 'dom' => intval(
                    $transaction->date->format('d')
                ), 'leeway' => 10, 'inactive' => false,
                          'beneficiary' => is_null(
                                  $transaction->beneficiary
                              ) ? 0 : $transaction->beneficiary->id,
                          'category' => is_null(
                                  $transaction->category
                              ) ? 0 : $transaction->category->id,
                          'budget' => is_null(
                                  $transaction->budget
                              ) ? 0 : $transaction->budget->id];
            $title .= ' based on "' . $transaction->description . '"';
        }

        if (Input::old()) {
            $prefilled = ['description' => Input::old('description'),
                          'amount'      => floatval(Input::old('amount')),
                          'leeway'      => intval(Input::old('leeway')),
                          'dom'         => intval(Input::old('dom')),
                          'beneficiary' => intval(Input::old('beneficiary_id')),
                          'category'    => intval(Input::old('category_id')),
                          'budget'      => intval(Input::old('budget_id')),
                          'inactive'    =>
                              intval(Input::old('inactive')) == 1 ? true : false

            ];
        }


        $list = PredictableHelper::componentList();

        return View::make('predictables.add')->with(
            'title', $title
        )->with('components', $list)->with('prefilled', $prefilled);
    }

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
            Queue::push('PredictableQueue@scan', $predictable);

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

    public function edit(Predictable $predictable)
    {
        // TODO do the prefilled thing just like add()
        if (!Input::old()) {
            Session::put('previous', URL::previous());
        }
        $list = PredictableHelper::componentList();

        // set some id's
        if (!is_null($predictable->beneficiary)) {
            $predictable->beneficiary_id = $predictable->beneficiary->id;
        }
        if (!is_null($predictable->category)) {
            $predictable->category_id = $predictable->category->id;
        }
        if (!is_null($predictable->budget)) {
            $predictable->budget_id = $predictable->budget->id;
        }

        return View::make('predictables.edit')->with(
            'title', 'Edit predictable "' . $predictable->description . '"'
        )->with('predictable', $predictable)->with('components', $list);
    }

    public function postEdit(Predictable $predictable)
    {

        $predictable->description = Input::get('description');
        $predictable->amount = floatval(Input::get('amount'));
        $predictable->dom = intval(Input::get('dom'));
        $predictable->pct = intval(Input::get('pct'));
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
            Queue::push('PredictableQueue@scan', $predictable);

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

    public function rescan(Predictable $predictable)
    {
        Queue::push('PredictableQueue@scan', $predictable);
        Session::flash(
            'success', 'Rescan was queued.'
        );

        return Redirect::route('predictableoverview', $predictable->id);
    }

    public function rescanAll(Predictable $predictable)
    {
        Queue::push('PredictableQueue@scanAll', $predictable);
        Session::flash(
            'success', 'Rescan was queued.'
        );

        return Redirect::route('predictableoverview', $predictable->id);
    }
}