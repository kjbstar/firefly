<?php
use Carbon\Carbon as Carbon;

/**
 * Class MetaHelper
 */
class ComponentHelper
{


    public static function mutations(Component $component, Carbon $date = null)
    {
        $transfersQuery = $component->transfers()->orderBy('date', 'DESC')->orderBy('id', 'DESC');
        $transactionsQuery = $component->transactions()->orderBy('date', 'DESC')->orderBy('id', 'DESC');
        if (!is_null($date)) {
            $transfersQuery->inMonth($date);
            $transactionsQuery->inMonth($date);
        }
        $transfers = $transfersQuery->get();
        echo count($transfers);
        $transactions = $transactionsQuery->get();

        if (count($transfers) > 0 && count($transactions) > 0) {
            $list = array_merge($transactions->toArray(), $transfers->toArray());
            usort($list, 'CompareSortMutations');
        } else {
            $list = [];
        }
        return $list;




    }

    /**
     * Generate an array containing all months starting one
     * year ago up until now, detailing various statistics of said month.
     *
     * @param Component $component
     *
     * @return array
     */
    public static function overviewOfMonths(Component $component)
    {
        $end = new Carbon;
        $end->addMonth();
        $start = Toolkit::getEarliestEvent();
        $list = [];
        while ($end > $start) {
            $query = $component->transactions()->inMonth($end);
            $url = URL::Route(
                OBJ . 'overview',
                [$component->id, $end->format('Y'), $end->format('m')]
            );
            $entry = [];
            $entry['title'] = $end->format('F Y');
            $entry['url'] = $url;
            $entry['sum'] = $query->sum('amount');
            $entry['avg'] = $query->avg('amount');
            $entry['count'] = $query->count();
            $entry['month'] = $end->format('m');
            $entry['year'] = $end->format('Y');
            $entry['limit'] = null;
            $limit = $component->limits()->inMonth($end)->first();
            if ($limit) {
                $entry['limit'] = $limit->amount;
                $entry['limit-id'] = $limit->id;
            }

            $end->subMonth();
            $list[] = $entry;
        }

        return $list;
    }

    /**
     * Generates a list of transactions made under the component
     * in a certain month.
     *
     * @param Component $component
     * @param Carbon    $date
     *
     * @return mixed
     */
    public static function generateTransactionListByMonth(
        Component $component, Carbon $date
    ) {
        return $component->transactions()->inMonth($date)->get();

    }

    /**
     * Get transactions without components of type X.
     *
     * @param        $type
     * @param Carbon $date
     *
     * @return array
     */
    public static function transactionsWithoutComponent(
        $type, Carbon $date = null
    ) {
        $query = Auth::user()->transactions()->orderBy('date', 'DESC');
        if (!is_null($date)) {
            $query->inMonth($date);
        }
        $list = [];
        foreach ($query->get() as $tr) {
            if (is_null($tr->$type)) {
                $list[] = $tr;
            }
        }

        return $list;

    }

    /**
     * Get a parent list for components of type.
     *
     * @param           $type
     * @param Component $component
     *
     * @return array
     */
    public static function getParentList($type, Component $component = null)
    {

        $parents = [0 => 'No parent'];
        $query = Auth::user()->components()->whereNull('parent_component_id')
            ->where('type', $type);
        if (!is_null($component)) {
            if ($component->childrenComponents()->count() > 0) {
                return $parents;
            }
            $query->where('id', '!=', $component->id);
        }
        $data = $query->get();

        foreach ($data as $b) {
            $parents[$b->id] = $b->name;
        }
        asort($parents);

        return $parents;
    }

    public static function emptyPrefilledAray()
    {
        return [
            'name'                => '',
            'parent_component_id' => 0,
            'reporting'           => false,
        ];
    }

    public static function prefilledFromOldInput()
    {
        return [
            'name'                => Input::old('name'),
            'parent_component_id' => intval(Input::old('parent_component_id')),
            'reporting'           => intval(Input::old('reporting')) == 1 ? true : false,
        ];
    }

    public static function prefilledFromComponent(Component $component)
    {
        return [
            'name'                => $component->name,
            'parent_component_id' => intval($component->parent_component_id),
            'reporting'           => $component->reporting == 1 ? true : false
        ];
    }

    public static function saveComponentFromText($type, $name)
    {
        $parts = explode('/', $name);
        if (count($parts) > 2) {
            Session::flash('error', 'Could not save ' . $type . ' "' . htmlentities($name) . '" due to errors.');
            return null;
        }

        if (count($parts) == 1) {
            return Component::findOrCreate($type, $name);
        }
        if (count($parts) == 2) {
            $parent = Component::findOrCreate($type, $parts[0]);
            $object = Component::findOrCreate($type, $parts[1]);
            $object->parent_component_id = $parent->id;
            $object->save();
            return $object;
        }
        return null;

    }

}

function CompareSortMutations($a, $b)
{
    $dateObjectA = new Carbon($a['date']);
    $dateObjectB = new Carbon($b['date']);
    $createdAtObjectA = new Carbon($a['created_at']);
    $createdAtObjectB = new Carbon($b['created_at']);

    if ($dateObjectA == $dateObjectB) {
        if ($createdAtObjectA == $createdAtObjectB) {
            return 0;
        } else {
            if ($createdAtObjectA < $createdAtObjectB) {
                return -1;
            } else {
                return 1;
            }
        }
    } else {
        if ($dateObjectA < $dateObjectB) {
            return -1;
        } else {
            return 1;
        }
    }

}