<?php
use Carbon\Carbon as Carbon;


/**
 * Class ListHelper
 */
class ListHelper
{
    /**
     * Get the transactions without a component in that month.
     * @param        $type
     * @param Carbon $date
     *
     * @return array
     */
    public static function transactionsWithoutComponentType(
        $type, Carbon $date
    ) {
        // hier de code voor alles zonder component van type "$type"
        $list = [];
        $query = Auth::user()->transactions()->with('components')->inMonth(
            $date
        )->orderBy('date', 'ASC')->orderBy('transactions.id', 'DESC');

        foreach ($query->get() as $tr) {
            if (!self::hasComponent($tr, $type)) {
                $list[] = $tr;
            }
        }

        return $list;
    }

    /**
     * Does the transaction have a component of type X?
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
     * Find all transactions with the component in that month.
     *
     * @param Component $component
     * @param Carbon    $date
     *
     * @return mixed
     */
    public static function transactionsWithComponent(
        Component $component, Carbon $date
    ) {
        $query = $component->transactions()->inMonth($date)->orderBy(
            'date', 'ASC'
        )->orderBy('transactions.id', 'DESC');

        return $query->get();

    }
} 