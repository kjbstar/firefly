<?php
require_once(app_path() . '/helpers/Toolkit.php');

/**
 * Class LimitController
 */
class LimitController extends BaseController
{

    /**
     * Add a limit to a certain component for a given year and month.
     *
     * @param \Component $component The component
     * @param int        $year      The year
     * @param int        $month     The month.
     *
     * @return \Illuminate\View\View
     */
    public function addLimit(Component $component, $year, $month)
    {
        Session::put('previous', URL::previous());
        $date = Toolkit::parseDate($year, $month);
        if ($date) {
            return View::make('meta-limit.add')->with(
                'object', $component
            )->with('date', $date);
        }

        App::abort(404);

        return View::make('error.404');
    }

    /**
     * Process the addition of a new limit.
     *
     * @param Component $component The component
     * @param int       $year      The year
     * @param int       $month     The month
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function postAddLimit(Component $component, $year, $month)
    {
        $date = Toolkit::parseDate($year, $month);
        if ($date) {
            $limit = new Limit(['component_id' => $component->id,
                                'date'         => $date, 'amount' => floatval(
                    Input::get('amount')
                )]);

            $validator = Validator::make($limit->toArray(), Limit::$rules);
            if ($validator->fails()) {
                Session::flash('error', 'Could not add ' . OBJ . ' limit.');

                return Redirect::route(OBJ . 'overview', [$component->id]);
            } else {
                $limit->save();

                return Redirect::to(Session::get('previous'));
            }
        }

        App::abort(404);

        return View::make('error.404');
    }

    /**
     * Edit a limit (show the view).
     *
     * @param Limit $limit The limit
     *
     * @return \Illuminate\View\View
     */
    public function editLimit(Limit $limit)
    {
        Session::put('previous', URL::previous());
        $object = Auth::user()->components()->find($limit->component_id);
        if ($object) {
            return View::make('meta-limit.edit')->with('object', $object)->with(
                'limit', $limit
            );
        }

        App::abort(404);

        return View::make('error.404');
    }

    /**
     * Process the editing of a limit.
     *
     * @param Limit $limit The limit
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function postEditLimit(Limit $limit)
    {
        $object = Auth::user()->components()->find($limit->component_id);
        if ($object) {
            $limit->amount = floatval(Input::get('amount'));

            $validator = Validator::make($limit->toArray(), Limit::$rules);
            if ($validator->fails()) {
                Session::flash('error', 'Could not edit ' . OBJ . 'limit.');

                return Redirect::route(OBJ . 'overview', [$object->id]);
            } else {
                $limit->save();

                return Redirect::to(Session::get('previous'));
            }
        }

        App::abort(404);

        return View::make('error.404');
    }

    /**
     * Delete a limit (shows the view)
     *
     * @param Limit $limit The limit
     *
     * @return \Illuminate\View\View
     */
    public function deleteLimit(Limit $limit)
    {
        Session::put('previous', URL::previous());
        $object = Auth::user()->components()->find($limit->component_id);
        if ($object) {
            return View::make('meta-limit.delete')->with('object', $object)
                ->with('date', $limit->date);
        }

        App::abort(404);

        return View::make('error.404');
    }

    /**
     * Process the deletion.
     *
     * @param Limit $limit The limit
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function postDeleteLimit(Limit $limit)
    {
        $object = Auth::user()->components()->find($limit->component_id);
        if ($object) {
            $limit->delete();
            Session::flash('success', 'Limit removed.');

            return Redirect::to(Session::get('previous'));
        }

        App::abort(404);

        return View::make('error.404');
    }
} 