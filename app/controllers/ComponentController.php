<?php
/** @noinspection PhpIncludeInspection */
require_once(app_path() . '/helpers/ComponentHelper.php');
/** @noinspection PhpIncludeInspection */
require_once(app_path() . '/helpers/Toolkit.php');

/**
 * Class ComponentController
 */
class ComponentController extends BaseController
{

    /**
     * Shows the index of the object.
     *
     * @return View
     */
    public function showIndex()
    {
        $objects = Auth::user()->components()->whereNull('parent_component_id')->with('childrencomponents')->where(
            'type', OBJ
        )->get();
        $result = [];
        $parents = []; // used in for multisort.
        foreach ($objects as $obj) {
            $parents[] = $obj->name;

            $current = [
                'id'       => $obj->id,
                'name'     => $obj->name,
                'children' => []
            ];

            // used in for multisort.
            $names = [];
            foreach ($obj->childrencomponents as $c) {

                $names[] = $c->name;
                $child = [
                    'id'   => $c->id,
                    'name' => $c->name
                ];
                // add to array:
                $current['children'][] = $child;
            }
            array_multisort($names, SORT_NATURAL, $current['children']);

            $result[] = $current;
        }

        array_multisort($parents, SORT_STRING, $result);

        return View::make('components.index')->with('title', 'All ' . OBJS)->with('objects', $result);
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

        return View::make('components.empty')->with('title', 'Transactions without a ' . OBJ)->with('mutations', $list)
            ->with('date', $date);
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
            $prefilled = ComponentHelper::emptyPrefilledAray();
        } else {
            $prefilled = ComponentHelper::prefilledFromOldInput();
        }
        $parents = ComponentHelper::getParentList(OBJ);

        return View::make('components.add')->with('title', 'Add new ' . OBJ)->with('parents', $parents)->with(
            'prefilled', $prefilled
        );
    }

    /**
     * Process adding of new object.
     *
     * @return Redirect
     */
    public function postAdd()
    {
        $parentID = intval(Input::get('parent_component_id')) > 0 ? intval(Input::get('parent_component_id')) : null;
        /** @noinspection PhpUndefinedFieldInspection */
        $data = [
            'name'                => Input::get('name'),
            'parent_component_id' => $parentID,
            'user_id'             => Auth::user()->id,
            'reporting'           => Input::get('reporting') == '1' ? 1 : 0,
            'type'                => OBJ
        ];


        $object = new Component($data);
        $validator = Validator::make($object->toArray(), Component::$rules);
        // validation fails!
        if ($validator->fails()) {
            Log::error('Could not save component: ' . print_r($validator->messages()->all(), true));
            Session::flash('error', 'Could not save the new ' . OBJ);
            return Redirect::route('add' . OBJ)->withErrors($validator)->withInput();
        } else {
            $result = $object->save();
            // it fails again!
            if (!$result) {
                Log::error('Could not save component, trigger failure!');
                Session::flash('error', 'Could not save the new ' . OBJ . '. Is the name unique?');
                return Redirect::route('add' . OBJ)->withErrors($validator)->withInput();

            }
            Session::flash('success', 'The new ' . OBJ . ' has been saved.');
            return Redirect::to(Session::get('previous'));
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
            $prefilled = ComponentHelper::prefilledFromComponent($component);
        } else {
            $prefilled = ComponentHelper::prefilledFromOldInput();
        }
        $parents = ComponentHelper::getParentList(OBJ, $component);
        return View::make('components.edit')->with('object', $component)->with('parents', $parents)->with(
            'title', 'Edit ' . OBJ . ' ' . $component->name
        )->with('prefilled', $prefilled);
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
            = intval(Input::get('parent_component_id')) > 0 ? intval(Input::get('parent_component_id')) : null;
        $component->name = Input::get('name');
        $component->reporting = Input::get('reporting') == '1' ? 1 : 0;
        $validator = Validator::make($component->toArray(), Component::$rules);
        // it fails!
        if ($validator->fails()) {
            Session::flash('error', 'Could not save the ' . OBJ . '.');
            return Redirect::route('edit' . OBJ, $component->id)->withErrors($validator)->withInput();
        } else {
            $result = $component->save();
            // it fails again!
            if (!$result) {
                Session::flash('error', 'Could not save the ' . OBJ . '. Is the name unique?');
                return Redirect::route('edit' . OBJ, $component->id)->withInput()->withErrors($validator);
            }
            Session::flash('success', 'The ' . OBJ . ' has been updated.');
            return Redirect::to(Session::get('previous'));


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

        return View::make('components.delete')->with('object', $component)->with(
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
        Session::flash('success', OBJ . ' deleted.');

        return Redirect::to(Session::get('previous'));
    }

    /**
     * @param Component $component
     *
     * @return \Illuminate\View\View
     */
    public function showOverview(Component $component)
    {
        $parent = is_null($component->parent_component_id) ? null : $component->parentComponent()->first();
        $months = ComponentHelper::months($component);
        $title = 'Overview for ' . OBJ . ' "' . $component->name . '"';

        return View::make('components.overview')->with('title', $title)->with('component', $component)->with(
            'months', $months
        )->with('parent', $parent);
    }

    /**
     * @param Component $component
     * @param           $year
     * @param           $month
     *
     * @return \Illuminate\View\View
     */
    public function showOverviewByMonth(Component $component, $year, $month)
    {
        $date = Toolkit::parseDate($year, $month);
        $mutations = ComponentHelper::mutations($component, $date);
        $title = 'Overview for ' . OBJ . ' "' . $component->name . '" in ' . $date->format('F Y');
        return View::make('components.overview-by-month')->with('component', $component)->with('title', $title)->with(
            'mutations', $mutations
        )->with('date', $date);
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