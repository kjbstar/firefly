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
    public static function generateOverviewOfMonths(Component $component)
    {
        $end = new Carbon;
        $end->addMonth();
        $start = new Carbon('first day of january 2013');
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
     * @param $type
     *
     * @return array
     */
    public static function transactionsWithoutComponent(
        $type, Carbon $date = null
    ) {
        $query = Auth::user()->transactions()->orderBy('date', 'DESC')->with(
            'components'
        );
        if (!is_null($date)) {
            $query->inMonth($date);
        }
        $list = [];
        foreach ($query->get() as $tr) {
            if (!self::hasComponent($tr, $type)) {
                $list[] = $tr;
            }
        }

        return $list;

    }

    /**
     * Does the transaction have a component of type X?
     *
     * @param Transaction $transaction
     * @param             $type
     *
     * @return bool
     */

    public static function hasComponent(Transaction $transaction, $type)
    {
        foreach ($transaction->components()->get() as $component) {
            if ($component->type === $type) {
                return true;
            }
        }

        return false;
    }


    /**
     * Get a parent list for components of type.
     *
     * @param $type
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

}