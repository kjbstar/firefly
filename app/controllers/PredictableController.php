<?php
use Carbon\Carbon as Carbon;

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

    public function addByTransaction(Transaction $transaction)
    {
        Session::put('previous', URL::previous());

        return View::make('predictables.addbytransaction')->with(
            'transaction', $transaction
        );
    }

    public function postAddByTransaction(Transaction $transaction)
    {
        // create new predictable.
        $pred = new Predictable();
        $pred->user()->associate(Auth::user());
        $pred->amount = floatval(Input::get('amount'));
        $pred->description = Input::get('description');
        $pred->dom = intval(Input::get('dom'));
        $pred->pct = intval(Input::get('pct'));
        $pred->save();

        foreach (Input::get('component') as $id) {
            $component = Auth::user()->components()->find($id);
            if ($component) {
                $pred->components()->save($component);
            }
        }
        Queue::push(
            'PredictableQueue@processPredictable', $pred
        );
        Session::flash('success', 'Predictable saved.');

        return Redirect::to(Session::get('previous'));
    }

}