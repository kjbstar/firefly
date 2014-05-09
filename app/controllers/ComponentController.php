<?php

/**
 * Class ComponentController
 */
class ComponentController extends BaseController
{

    /**
     * Show the index.
     *
     * @param Type $type
     *
     * @return \Illuminate\View\View
     */
    public function index(Type $type)
    {
        $components = ComponentHelper::indexList($type);
        return View::make('components.index')->with('title', 'All ' . Str::plural($type->type))->with(
            'components', $components
        )->with('type', $type);
    }

    /**
     * Show a list of transactions without this type of component.
     *
     * @param Type $type
     * @param null $year
     * @param null $month
     *
     * @return \Illuminate\View\View
     */
    public function noComponent(Type $type, $year = null, $month = null)
    {
        $date = Toolkit::parseDate($year, $month);
        $list = ComponentHelper::transactionsWithoutComponent($type, $date);

        return View::make('components.empty')->with('title', 'Transactions without a ' . $type->type)->with(
            'mutations', $list
        )->with('date', $date)->with('type', $type);
    }

    /**
     * Add a new component
     *
     * @param Type $type
     *
     * @return \Illuminate\View\View
     */
    public function add(Type $type)
    {
        if (!Input::old()) {
            Session::put('previous', URL::previous());
            $prefilled = ComponentHelper::emptyPrefilledAray();
        } else {
            $prefilled = ComponentHelper::prefilledFromOldInput();
        }

        $parents = ComponentHelper::getParentList($type);

        return View::make('components.add')->with('title', 'Add new ' . $type->type)->with('parents', $parents)->with(
            'prefilled', $prefilled
        )->with('type', $type);
    }

    /**
     * Process add of new component.
     *
     * @param Type $type
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postAdd(Type $type)
    {
        // get data
        $parentID = intval(Input::get('parent_component_id')) > 0 ? intval(Input::get('parent_component_id')) : null;
        $data = [
            'name'                => Input::get('name'),
            'parent_component_id' => $parentID,
            'reporting'           => Input::get('reporting') == '1' ? 1 : 0,
        ];
        // create the new component
        $component = new Component($data);
        /** @noinspection PhpParamsInspection */
        $component->user()->associate(Auth::user());
        $component->type()->associate($type);

        // validate it:
        $validator = Validator::make($component->toArray(), Component::$rules);

        // validation fails!
        if ($validator->fails()) {
            Session::flash('error', 'Could not save the new ' . $type->type);
            return Redirect::route('addcomponent', $type->id)->withErrors($validator)->withInput();
        }
        // try to save it:
        $result = $component->save();


        // it fails again!
        if (!$result) {
            Session::flash('error', 'Could not save the new ' . $type->type . '. Is the name unique?');
            return Redirect::route('addcomponent', $type->id)->withErrors($validator)->withInput();

        }

        // success!
        Session::flash('success', 'The new ' . $type->type . ' has been saved.');
        return Redirect::to(Session::get('previous'));
    }

    /**
     * Edit a component.
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
            $prefilled = ComponentHelper::prefilledFromOldInput($component);
        }

        $parents = ComponentHelper::getParentList($component->type, $component);
        return View::make('components.edit')->with('component', $component)->with('parents', $parents)->with(
            'title', 'Edit ' . $component->type->type . ' "' . $component->name . '"'
        )->with('prefilled', $prefilled);
    }

    /**
     * Edit a component.
     *
     * @param Component $component The component.
     *
     * @return Redirect
     */
    public function postEdit(Component $component)
    {
        // save icon (move to helper)
        ComponentHelper::saveIcon($component);

        // update component
        $component->parent_component_id
            = intval(Input::get('parent_component_id')) > 0 ? intval(Input::get('parent_component_id')) : null;
        $component->name = Input::get('name');
        $component->reporting = Input::get('reporting') == '1' ? 1 : 0;

        // validate it:
        $validator = Validator::make($component->toArray(), Component::$rules);

        // it fails!
        if ($validator->fails()) {
            Session::flash('error', 'Could not save the ' . $component->type->type . '.');
            return Redirect::route('editcomponent', $component->id)->withErrors($validator)->withInput();
        }

        // try to save it
        $result = $component->save();

        // it fails again!
        if (!$result) {
            Session::flash('error', 'Could not save the ' . $component->type->type . '. Is the name unique?');
            return Redirect::route('editcomponent', $component->id)->withInput()->withErrors($validator);
        }

        // return!
        Session::flash('success', 'The ' . $component->type->type . ' has been updated.');
        return Redirect::to(Session::get('previous'));


    }

    /**
     * Delete an component.
     *
     * @param Component $component The component.
     *
     * @return View
     */
    public function delete(Component $component)
    {
        Session::put('previous', URL::previous());

        $transactions = $component->transactions()->count();
        $transfers = $component->transfers()->count();

        return View::make('components.delete')->with('component', $component)->with(
            'title', 'Delete ' . $component->type->type . ' "' . $component->name . '"'
        )->with('transactions', $transactions)->with('transfers', $transfers);
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
        Session::flash('success', ucfirst($component->type->type) . ' deleted.');

        return Redirect::route('components', $component->type->id);
    }

    /**
     * @param Component $component
     *
     * @return \Illuminate\View\View
     */
    public function overview(Component $component)
    {
        $parent = $component->parentComponent;
        $months = ComponentHelper::months($component);
        $title = 'Overview for ' . $component->type->type . ' "' . $component->name . '"';

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
    public function overviewByMonth(Component $component, $year, $month)
    {
        $date = Toolkit::parseDate($year, $month);
        $mutations = ComponentHelper::mutations($component, $date);
        $title = 'Overview for ' . $component->type->type . ' "' . $component->name . '" in ' . $date->format('F Y');

        return View::make('components.overview-by-month')->with('component', $component)->with('title', $title)->with(
            'mutations', $mutations
        )->with('date', $date);
    }

    /**
     * Returns a typeahead list.
     *
     * @param Type $type
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function typeahead(Type $type)
    {
        $components = Auth::user()->components()->where('type_id', $type->id)->get();
        $return = [];
        foreach ($components as $o) {
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

    /**
     * Render icon for component.
     *
     * @param Component $component
     */
    // @codeCoverageIgnoreStart
    public function renderIcon(Component $component)
    {
        if (!$component->hasIcon()) {
            App::abort(404);
        } else {

            $image = imagecreatefrompng($component->iconFileLocation());
            imageAlphaBlending($image, true);
            imageSaveAlpha($image, true);

            header('Content-Type: image/png');
            imagepng($image);
            imagedestroy($image);
            exit();

        }
    }
    // @codeCoverageIgnoreEnd
}