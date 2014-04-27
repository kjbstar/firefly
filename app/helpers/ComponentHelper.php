<?php
use Carbon\Carbon as Carbon;

/**
 * Class MetaHelper
 */
class ComponentHelper
{


    /**
     * Generate an array containing all months starting one
     * year ago up until now, detailing various statistics of said month.
     *
     * @param Component $component
     *
     * @return array
     */
    public static function months(Component $component)
    {
        $end = new Carbon;
        $end->addMonth();
        $start = Toolkit::getEarliestEvent();
        $list = [];
        while ($end > $start) {
            $query = $component->transactions()->inMonth($end);
            $url = URL::Route(OBJ . 'overviewmonth', [$component->id, $end->format('Y'), $end->format('m')]);
            $entry = [
                'title' => $end->format('F Y'),
                'url'   => $url,
                'sum'   => $query->sum('amount'),
                'count' => $query->count(),
                'month' => $end->format('m'),
                'year'  => $end->format('Y'),
                'limit' => null
            ];
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
     * @param Component $component
     * @param Carbon    $date
     *
     * @return mixed
     */
    public static function mutations(Component $component, Carbon $date)
    {
        $transactions = $component->transactions()->inMonth($date)->get();
        $transfers = $component->transfers()->inMonth($date)->get();
        $result = $transactions->merge($transfers);
        $result = $result->sortBy(
            function ($a) {
                return $a->created_at;
            }
        )->reverse();
        return $result;
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

        $query = Auth::user()->transactions()->whereNotIn(
            'id', function ($query) use ($type) {
                $query->select('transactions.id')->from('transactions')->leftJoin(
                    'component_transaction', 'component_transaction.transaction_id', '=', 'transactions.id'
                )->leftJoin('components', 'components.id', '=', 'component_transaction.component_id')->where(
                        'components.type', $type
                    );
            }
        )->orderBy('date', 'DESC');
        if (!is_null($date)) {
            $query->inMonth($date);
        }
        return $query->get();
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

    /**
     * @return array
     */
    public static function emptyPrefilledAray()
    {
        return [
            'name'                => '',
            'parent_component_id' => 0,
            'reporting'           => false,
            'hasIcon'             => false,
            'iconTag'             => ''
        ];
    }

    /**
     * @return array
     */
    public static function prefilledFromOldInput()
    {
        $data = [
            'name'                => Input::old('name'),
            'parent_component_id' => intval(Input::old('parent_component_id')),
            'reporting'           => intval(Input::old('reporting')) == 1 ? true : false,
            'hasIcon'             => false,
            'iconTag'             => ''
        ];
        return $data;

    }

    /**
     * @param Component $component
     *
     * @return array
     */
    public static function prefilledFromComponent(Component $component)
    {
        return [
            'name'                => $component->name,
            'parent_component_id' => intval($component->parent_component_id),
            'reporting'           => $component->reporting == 1 ? true : false,
            'hasIcon'             => $component->hasIcon(),
            'iconTag'             => $component->iconTag()
        ];
    }

    /**
     * @param $type
     * @param $name
     *
     * @return Component|null
     */
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

/**
 * @param $a
 * @param $b
 *
 * @return int
 */
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