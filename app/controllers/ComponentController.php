<?php
// @codeCoverageIgnoreStart
/** @noinspection PhpIncludeInspection */
require_once(app_path() . '/helpers/ComponentHelper.php');
/** @noinspection PhpIncludeInspection */
require_once(app_path() . '/helpers/Toolkit.php');
// @codeCoverageIgnoreEnd

/**
 * Class ComponentController
 */
class ComponentController extends BaseController
{

    /**
     * Shows the index of the component.
     *
     * @return View
     */
    public function index(Type $type)
    {
        $components = Auth::user()->components()->whereNull('parent_component_id')->with('childrencomponents')->where(
            'type_id', $type->id
        )->get();
        $result = [];
        $parents = []; // used in for multisort.
        foreach ($components as $obj) {
            $parents[] = $obj->name;

            $current = [
                'id'       => $obj->id,
                'name'     => $obj->name,
                'hasIcon'  => $obj->hasIcon(),
                'iconTag'  => $obj->iconTag(),
                'children' => []
            ];

            // used in for multisort.
            $names = [];
            foreach ($obj->childrencomponents as $c) {

                $names[] = $c->name;
                $child = [
                    'id'      => $c->id,
                    'name'    => $c->name,
                    'hasIcon' => $c->hasIcon(),
                    'iconTag' => $c->iconTag(),
                ];
                // add to array:
                $current['children'][] = $child;
            }
            array_multisort($names, SORT_NATURAL, $current['children']);

            $result[] = $current;
        }

        array_multisort($parents, SORT_STRING, $result);

        return View::make('components.index')->with('title', 'All ' . Str::plural($type->type))->with(
            'components', $result
        )->with('type', $type);
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
     * Add a new component.
     *
     * @return View
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
     * Process adding of new component.
     *
     * @return Redirect
     */
    public function postAdd(Type $type)
    {


        $parentID = intval(Input::get('parent_component_id')) > 0 ? intval(Input::get('parent_component_id')) : null;
        /** @noinspection PhpUndefinedFieldInspection */
        $data = [
            'name'                => Input::get('name'),
            'parent_component_id' => $parentID,
            'user_id'             => Auth::user()->id,
            'reporting'           => Input::get('reporting') == '1' ? 1 : 0,
        ];


        $component = new Component($data);
        $component->type()->associate($type);
        $validator = Validator::make($component->toArray(), Component::$rules);
        // validation fails!
        if ($validator->fails()) {
            Log::error('Could not save component: ' . print_r($validator->messages()->all(), true));
            Session::flash('error', 'Could not save the new ' . $type->type);
            return Redirect::route('add' . OBJ)->withErrors($validator)->withInput();
        } else {
            $result = $component->save();
            // it fails again!
            if (!$result) {
                Log::error('Could not save component, trigger failure!');
                Session::flash('error', 'Could not save the new ' . $type->type . '. Is the name unique?');
                return Redirect::route('addcomponent', $type->id)->withErrors($validator)->withInput();

            }
            Session::flash('success', 'The new ' . $type->type . ' has been saved.');
            return Redirect::to(Session::get('previous'));
        }
    }

    /**
     * Edit an component.
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
     * Edit an component.
     *
     * @param Component $component The component.
     *
     * @return Redirect
     */
    public function postEdit(Component $component)
    {
        if (Input::hasFile('icon')) {
            $icon = Input::file('icon');
            $mime = $icon->getMimeType();
            if ($mime == 'image/png') {
                // continue:
                $path = $icon->getRealPath();
                $size = getimagesize($path);
                if ($size[0] == 16 && $size[1] == 16) {
                    // continue again!
                    $destinationPath = Component::getDestinationPath();
                    $fileName = $component->id . '.png';
                    $icon->move($destinationPath, $fileName);
                }
            }
        }

        $component->parent_component_id
            = intval(Input::get('parent_component_id')) > 0 ? intval(Input::get('parent_component_id')) : null;
        $component->name = Input::get('name');
        $component->reporting = Input::get('reporting') == '1' ? 1 : 0;
        $validator = Validator::make($component->toArray(), Component::$rules);
        // it fails!
        if ($validator->fails()) {
            Session::flash('error', 'Could not save the ' . $component->type->type . '.');
            return Redirect::route('editcomponent', $component->id)->withErrors($validator)->withInput();
        } else {
            $result = $component->save();
            // it fails again!
            if (!$result) {
                Session::flash('error', 'Could not save the ' . $component->type->type . '. Is the name unique?');
                return Redirect::route('editcomponent', $component->id)->withInput()->withErrors($validator);
            }
            Session::flash('success', 'The ' . $component->type->type . ' has been updated.');
            return Redirect::to(Session::get('previous'));


        }
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
        if (!Input::old()) {
            Session::put('previous', URL::previous());
        }

        return View::make('components.delete')->with('component', $component)->with(
            'title', 'Delete ' . $component->type->type . ' "' . $component->name . '"'
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
        Session::flash('success', $component->type->type . ' deleted.');

        return Redirect::to(Session::get('previous'));
    }

    /**
     * @param Component $component
     *
     * @return \Illuminate\View\View
     */
    public function overview(Component $component)
    {
        $parent = is_null($component->parent_component_id) ? null : $component->parentComponent()->first();
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
     * Generate a typeahead compatible component list.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function typeahead()
    {
        $components = Auth::user()->components()->where('type', OBJ)->get();
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

    public function renderIcon(Component $component)
    {
        if (!$component->hasIcon()) {
            App::abort(404);
        } else {
            $im = imagecreatefrompng($component->iconFileLocation());
            imageAlphaBlending($im, true);
            imageSaveAlpha($im, true);

            header('Content-Type: image/png');
            imagepng($im);
            imagedestroy($im);
            exit();
        }

    }
}