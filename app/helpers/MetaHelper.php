<?php
require_once(app_path() . '/helpers/ListHelper.php');


use Carbon\Carbon as Carbon;

/**
 * Class MetaHelper
 */
class MetaHelper
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
    public static function transactionsWithoutComponent($type)
    {
        $query = Auth::user()->transactions()->with('components');
        $list = [];
        foreach ($query->get() as $tr) {
            if (!ListHelper::hasComponent($tr, $type)) {
                $list[] = $tr;
            }
        }

        return $list;

    }

    /**
     * Get a parent list for components of type.
     *
     * @param $type
     *
     * @return array
     */
    public static function getParentList($type)
    {
        $parents = [0 => 'No parent'];
        $data = Auth::user()->components()->whereNull('parent_component_id')
            ->where('type', $type)->get();

        foreach ($data as $b) {
            $parents[$b->id] = $b->name;
        }
        asort($parents);

        return $parents;
    }

    /**
     * Get the chart data for a yearly chart.
     *
     * @param Component $component
     *
     * @return array
     */
    public static function chartDataForYear(Component $component)
    {
        $data = [];
        $start = new Carbon;
        $end = clone $start;
        $start->startOfMonth()->subYear();

        while ($start < $end) {
            $current = clone $start;
            $transactions = $component->transactions()->inMonth($current);
            $average = floatval($transactions->avg('amount'));
            $total = floatval($transactions->sum('amount'));
            $set = [];
            $set['date'] = clone $current;
            $set['average_spent'] = null;
            $set['average_earned'] = null;
            if ($average < 0) {
                $set['average_spent'] = $average * -1;
            } else {
                $set['average_earned'] = $average;
            }

            $set['total_spent'] = null;
            $set['total_earned'] = null;
            if ($total < 0) {
                $set['total_spent'] = $total * -1;
            } else {
                $set['total_earned'] = $total;
            }
            $set['count'] = intval($transactions->count());

            $data[] = $set;
            $start->addMonth();
        }

        return $data;
    }

    /**
     * Get the chart data for a specific component.
     *
     * @param Component $component
     * @param Carbon    $start
     *
     * @return array
     */
    public static function chartDataForMonth(
        Component $component, Carbon $start
    ) {
        $data = [];
        $end = clone $start;
        $end->endOfMonth();

        while ($start < $end) {
            $current = clone $start;
            $transactions = $component->transactions()->onDay($current);
            $average = floatval($transactions->avg('amount'));
            $total = floatval($transactions->sum('amount'));
            $set = [];
            $set['date'] = clone $current;
            $set['average_spent'] = null;
            $set['average_earned'] = null;
            if ($average < 0) {
                $set['average_spent'] = $average * -1;
            } else {
                $set['average_earned'] = $average;
            }

            $set['total_spent'] = null;
            $set['total_earned'] = null;
            if ($total < 0) {
                $set['total_spent'] = $total * -1;
            } else {
                $set['total_earned'] = $total;
            }
            $set['count'] = intval($transactions->count());

            $data[] = $set;
            $start->addDay();
        }

        return $data;
    }
} 