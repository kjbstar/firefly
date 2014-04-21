<?php

/**
 * Class PredictableHelper
 */
class PredictableHelper
{
    /**
     * @return array
     */
    public static function componentList()
    {
        $list = ['beneficiary' => [0 => '(none)'], 'budget' => [0 => '(none)'],
                 'category'    => [0 => '(none)']];
        $components = Auth::user()->components()->get();
        foreach ($components as $component) {
            $t = $component->type;
            $id = $component->id;
            $name = $component->name;

            // is a parent:
            if ($component->childrenComponents()->count() > 0) {
                $list[$t][$name] = isset($list[$t][$name]) ? $list[$t][$name]
                    : [];

            }

            // is a child:
            if (!is_null($component->parent_component_id)) {
                $parentName = $component->parentComponent()->first()->name;
                $list[$t][$parentName][$id] = $name;
            }
            // neither:
            if (is_null($component->parent_component_id)
                && $component->childrenComponents()->count() == 0
            ) {
                $list[$t][$id] = $name;
            }
        }

        return $list;
    }

    /**
     * @return array
     */
    public static function emptyPrefilledAray()
    {
        return [
            'description' => '',
            'amount'      => 0,
            'pct'         => 10,
            'dom'         => 1,
            'beneficiary' => 0,
            'category'    => 0,
            'budget'      => 0,
            'inactive'    => false,
            'account_id' => 0
        ];
    }

    /**
     * @return array
     */
    public static function prefilledFromOldInput()
    {
        return [
            'description' => Input::old('description'),
            'amount'      => floatval(Input::old('amount')),
            'pct'         => intval(Input::old('pct')),
            'dom'         => intval(Input::old('dom')),
            'beneficiary' => intval(Input::old('beneficiary_id')),
            'category'    => intval(Input::old('category_id')),
            'budget'      => intval(Input::old('budget_id')),
            'inactive'    => intval(Input::old('inactive')) == 1 ? true : false,
            'account_id'  => intval(Input::old('account_id')),

        ];
    }

    /**
     * @param Transaction $transaction
     *
     * @return array
     */
    public static function prefilledFromTransaction(Transaction $transaction)
    {
        return [
            'description' => $transaction->description,
            'amount'      => floatval($transaction->amount),
            'dom'         => intval($transaction->date->format('d')),
            'pct'         => 10,
            'inactive'    => false,
            'beneficiary' => is_null($transaction->beneficiary) ? 0 : $transaction->beneficiary->id,
            'category'    => is_null($transaction->category) ? 0 : $transaction->category->id,
            'budget'      => is_null($transaction->budget) ? 0 : $transaction->budget->id,
            'account_id'  => $transaction->account_id
        ];
    }

    /**
     * @param Predictable $predictable
     *
     * @return array
     */
    public static function prefilledFromPredictable(Predictable $predictable)
    {
        return [
            'description' => $predictable->description,
            'amount'      => floatval($predictable->amount),
            'dom'         => intval($predictable->dom),
            'pct'         => intval($predictable->pct),
            'inactive'    => intval($predictable->inactive) == 1 ? true : false,
            'beneficiary' => is_null($predictable->beneficiary) ? 0 : $predictable->beneficiary->id,
            'category'    => is_null($predictable->category) ? 0 : $predictable->category->id,
            'budget'      => is_null($predictable->budget) ? 0 : $predictable->budget->id,
            'account_id'  => $predictable->account_id
        ];
    }

}