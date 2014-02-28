<?php
require_once(app_path() . '/helpers/ComponentHelper.php');
require_once(app_path() . '/helpers/Toolkit.php');

/**
 * File contains the MetaController
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
 * Class MetaController
 *
 * This class can handle all three types of meta-information:
 * the budget, the beneficiary and the category.
 *
 * In a future version, this might be extended towards one single "field"
 * with another table.
 *
 * @category AccountController
 * @package  Controllers
 * @author   Sander Dorigo <sander@dorigo.nl>
 * @license  GPL 3.0
 * @link     http://www.sanderdorigo.nl/
 */
class ComponentController extends BaseController
{

    /**
     * Shows the index of the object.
     * TODO see todo on index
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
            $current['count'] = $obj->transactions()->count();
            $current['children'] = [];

            $children = $obj->childrenComponents()->get();

            $names = []; // for multisort.
            foreach ($children as $c) {
                $child = [];
                $names[] = $c->name;
                $child['id'] = $c->id;
                $child['name'] = $c->name;
                $child['count'] = $c->transactions()->count();
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
     * Shows all transactions without X.
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

    public function showAverageChart()
    {
        $objects = Auth::user()->components()->where('type', OBJ)->get();
        $chartData = [];
        $chart = App::make('gchart');
        $chart->addColumn(OBJ, 'string');
        $chart->addColumn('Average amount per transaction', 'number');

        foreach ($objects as $object) {
            $average = $object->transactions()->expenses()->avg('amount');
            $count = $object->transactions()->expenses()->count();
            if ($count >= 5) {
                $chartData[] = ['name'    => $object->name,
                                'average' => floatval($average)];
            }
            //$chart->addRow($object->name, floatval($average));
        }
        $amount = [];
        foreach ($chartData as $key => $row) {
            $amount[$key] = $row['average'];
        }
        array_multisort($amount, SORT_ASC, $chartData);

        $index = 0;
        foreach ($chartData as $entry) {
            if ($index < 35) {
                $chart->addRow($entry['name'], $entry['average']);
            }
            $index++;
        }


        $chart->generate();

        return Response::json($chart->getData());

    }

    /**
     * Add a new object.
     *
     * @return View
     */
    public function add()
    {
        Session::put('previous', URL::previous());
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
                    'Could not save the new '.OBJ.'. Is the name unique?'
                );

                return Redirect::route('add'.OBJ)->withErrors($validator)
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
        Session::put('previous', URL::previous());
        $parents = ComponentHelper::getParentList(OBJ,$component);
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
            if($result) {
                Session::flash('success', 'The '.OBJ.' has been updated.');
                return Redirect::to(Session::get('previous'));
            } else {
                Session::flash(
                    'error',
                    'Could not save the '.OBJ.'. Is the name unique?'
                );
                return Redirect::route('edit'.OBJ, $component->id)->withInput()
                    ->withErrors($validator);
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
        Session::put('previous', URL::previous());

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
        $date = Toolkit::parseDate($year, $month);
        $parent = is_null($component->parent_component_id) ? null
            : $component->parentComponent()->first();

        // switch on the presence of a date:
        $display = 'transactions';
        if (is_null($date)) {
            // count the list of transactions:
            $count = $component->transactions()->count();
            if ($count > 50) {
                $display = 'months';
                $entries = ComponentHelper::generateOverviewOfMonths(
                    $component
                );
            } else {
                $entries = $component->transactions()->orderBy('date','DESC')->get();
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
     * Function that shows a nice chart for this object.
     *
     * @param Component $component The Component
     * @param int       $year      The year
     * @param int       $month     The month
     *
     * @return mixed
     */
    public function showOverviewChart(
        Component $component, $year = null, $month = null
    ) {

        $date = Toolkit::parseDate($year, $month);

        if (is_null($date)) {
            $results = ComponentHelper::chartDataForYear($component);
        } else {
            // use date, do overview for days.
            $results = ComponentHelper::chartDataForMonth($component, $date);
        }

        // make the chart:
        $chart = App::make('gchart');
        $chart->addColumn('Date', 'date');
        $chart->addColumn('Total transactions', 'number');
        $chart->addColumn('Average amount spent per transaction', 'number');
        $chart->addColumn('Total amount spent', 'number');
        $chart->addColumn('Average amount earned per transaction', 'number');
        $chart->addColumn('Total amount earned', 'number');

        foreach ($results as $row) {
            $chart->addRow(
                $row['date'], $row['count'], $row['average_spent'],
                $row['total_spent'], $row['average_earned'],
                $row['total_earned']
            );
        }
        $chart->generate();
        $data = $chart->getData();

        return Response::json($data);
    }

    /**
     * Generates a pie chart comparing A to B.
     */
    public function showPieChart()
    {
        $obj = Input::get('object');
        $compare = Input::get('compare');
        $objectID = intval(Input::get('id'));

        // validate date for monthly limited overview.
        $date = Toolkit::parseDate(Input::get('year'), Input::get('month'));

        $object = Component::where('type', $obj)->find($objectID);
        if ($object) {
            // here we get the data:
            // TODO budgets=compare, beneficiaries=object
            // get all transactions ID's from the main object.
            // join the other component.
            $query = $object->transactions()->with(
                ['components' => function ($query) use ($compare) {
                        $query->where('type', $compare);
                    }]
            );
            if ($date) {
                $query->inMonth($date);
            }
            $transactions = $query->get();
            $result = [];

            foreach ($transactions as $t) {
                foreach ($t->components as $component) {
                    $result[$component->name]
                        = !isset($result[$component->name]) ? $t->amount
                        : $result[$component->name] + $t->amount;
                }

            }
            // make a chart
            $chart = App::make('gchart');
            $chart->addColumn(ucfirst($compare) . ' name', 'string');
            $chart->addColumn('amount', 'number');
            // loop it and fill the chart:
            $index = 0;
            foreach ($result as $r => $amount) {
                if ($index < 11) {
                    if ($amount < 0) {
                        $amount = $amount * -1;
                    }
                    $chart->addRow($r, floatval($amount));
                }
                $index++;
            }
            $chart->generate();

            return $chart->getData();
        }

        App::abort(404);

        return View::make('error.404');
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