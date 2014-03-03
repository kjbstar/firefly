<?php
/** @noinspection PhpIncludeInspection */
require_once(app_path() . '/helpers/ComponentHelper.php');
/** @noinspection PhpIncludeInspection */
require_once(app_path() . '/helpers/Toolkit.php');

class ComponentController extends BaseController
{

    /**
     * Shows the index of the object.
     *
     * @return View
     */
    public function showIndex()
    {
        $objects = Auth::user()->components()->whereNull('parent_component_id')
            ->where('type', OBJ)->get();
        $result = [];
        $parents = []; // for multisort.
        foreach ($objects as $obj) {
            $current = [];
            $parents[] = $obj->name;
            $current['id'] = $obj->id;
            $current['name'] = $obj->name;
            $current['children'] = [];

            $children = $obj->childrenComponents()->get();

            $names = []; // for multisort.
            foreach ($children as $c) {
                $child = [];
                $names[] = $c->name;
                $child['id'] = $c->id;
                $child['name'] = $c->name;
                // add to array:
                $current['children'][] = $child;
            }
            array_multisort($names, SORT_NATURAL, $current['children']);

            $result[] = $current;
        }

        array_multisort($parents, SORT_STRING, $result);

        return View::make('components.index')->with('title', 'All ' . OBJS)
            ->with(
                'objects', $result
            );
    }

    /**
     * Shows all transactions without component of type X.
     *
     * @param int $year  The year
     * @param int $month the month
     *
     * @return View
     */
    public function showEmpty($year = null, $month = null)
    {
        $date = Toolkit::parseDate($year, $month);

        $list = ComponentHelper::transactionsWithoutComponent(OBJ, $date);

        return View::make('components.empty')->with(
            'title', 'Transactions without a ' . OBJ
        )->with(
                'transactions', $list
            )->with('date', $date);
    }

    /**
     * Add a new object.
     *
     * @return View
     */
    public function add()
    {
        if (!Input::old()) {
            Session::put('previous', URL::previous());
        }
        $parents = ComponentHelper::getParentList(OBJ);

        return View::make('components.add')->with('title', 'Add new ' . OBJ)
            ->with(
                'parents', $parents
            );
    }

    /**
     * Process adding of new object.
     *
     * @return Redirect
     */
    public function postAdd()
    {
        $parentID = intval(Input::get('parent_component_id')) > 0 ? intval(
            Input::get('parent_component_id')
        ) : null;
        $data = [];

        $data['name'] = Input::get('name');
        $data['parent_component_id'] = $parentID;
        $data['user_id'] = Auth::user()->id;
        $data['type'] = OBJ;


        $object = new Component($data);
        $validator = Validator::make($object->toArray(), Component::$rules);
        if ($validator->fails()) {
            return Redirect::route('add' . OBJ)->withErrors($validator)
                ->withInput();
        } else {
            $result = $object->save();
            if ($result) {
                Session::flash(
                    'success', 'The new ' . OBJ . ' has been saved.'
                );

                return Redirect::to(Session::get('previous'));
            } else {
                Session::flash(
                    'error',
                    'Could not save the new ' . OBJ . '. Is the name unique?'
                );

                return Redirect::route('add' . OBJ)->withErrors($validator)
                    ->withInput();
            }
        }
    }

    /**
     * Edit an object.
     *
     * @param Component $component The component
     *
     * @return View
     */
    public function edit(Component $component)
    {
        if (!Input::old()) {
            Session::put('previous', URL::previous());
        }
        $parents = ComponentHelper::getParentList(OBJ, $component);
        $component->parent_component_id = is_null(
            $component->parent_component_id
        )
            ? 0
            : intval(
                $component->parent_component_id
            );

        return View::make('components.edit')->with('object', $component)->with(
            'parents', $parents
        )->with('title', 'Edit ' . OBJ . ' ' . $component->name);
    }

    /**
     * Edit an object.
     *
     * @param Component $component The component.
     *
     * @return Redirect
     */
    public function postEdit(Component $component)
    {
        $component->parent_component_id
            = intval(Input::get('parent_component_id')) > 0 ? intval(
            Input::get('parent_component_id')
        ) : null;
        $component->name = Input::get('name');
        $validator = Validator::make($component->toArray(), Component::$rules);
        if ($validator->fails()) {
            return Redirect::route('edit' . OBJ, $component->id)->withErrors(
                $validator
            )->withInput();
        } else {
            $result = $component->save();
            if ($result) {
                Session::flash('success', 'The ' . OBJ . ' has been updated.');

                return Redirect::to(Session::get('previous'));
            } else {
                Session::flash(
                    'error',
                    'Could not save the ' . OBJ . '. Is the name unique?'
                );

                return Redirect::route('edit' . OBJ, $component->id)->withInput(
                )->withErrors($validator);
            }


        }
    }

    /**
     * Delete an object.
     *
     * @param Component $component The component.
     *
     * @return View
     */
    public function delete(Component $component)
    {
        if (!Input::old()) {
            Session::put('previous', URL::previous());
        }

        return View::make('components.delete')->with('object', $component)
            ->with(
                'title', 'Delete ' . OBJ . ' ' . $component->name
            );
    }

    /**
     * Actually delete it.
     *
     * @param Component $component The component.
     *
     * @return Redirect
     */
    public function postDelete(Component $component)
    {
        $component->delete();

        return Redirect::to(Session::get('previous'));
    }

    /**
     * Show a general overview of the object.
     *
     * @param Component $component The component
     * @param int       $year      The year
     * @param int       $month     The month
     *
     * @return string
     */
    public function showOverview(
        Component $component, $year = null, $month = null
    ) {
        $forceMontly = Input::get('monthly') == 'true' ? true : false;
        $date = Toolkit::parseDate($year, $month);
        $parent = is_null($component->parent_component_id) ? null
            : $component->parentComponent()->first();

        // switch on the presence of a date:
        $display = 'transactions';
        if (is_null($date)) {
            // count the list of transactions:
            $count = $component->transactions()->count();
            if ($count > 50 || $forceMontly) {
                $display = 'months';
                $entries = ComponentHelper::generateOverviewOfMonths(
                    $component
                );
            } else {
                $entries = $component->transactions()->orderBy('date', 'DESC')
                    ->get();
            }
        } else {
            $entries = ComponentHelper::generateTransactionListByMonth(
                $component, $date
            );
        }
        $title = 'Overview for ' . OBJ . ' "' . $component->name . '"';
        if (!is_null($date)) {
            $title .= ' in ' . $date->format('F Y');
        }


        return View::make('components.overview')->with('title', $title)->with(
            'component', $component
        )->with('transactions', $entries)->with('parent', $parent)->with(
                'date', $date
            )->with('display', $display);
    }

    /**
     * Generate a typeahead compatible component list.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function typeahead()
    {
        $objects = Auth::user()->components()->where('type', OBJ)->get();
        $return = [];
        foreach ($objects as $o) {
            $name = $o->name;
            $parent = $o->parentComponent()->first();
            if ($parent) {
                $name = $parent->name . '/' . $name;
            }
            $return[] = $name;
        }
        sort($return);

        return Response::json($return);
    }


} 