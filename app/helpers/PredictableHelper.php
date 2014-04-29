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
        $data = [
            'description' => '',
            'amount'      => 0,
            'pct'         => 10,
            'dom'         => 1,
            'inactive'    => false,
            'account_id'  => 0
        ];
        foreach (Type::allTypes() as $type) {
            $data[$type->type] = '';
        }
        return $data;
    }

    /**
     * @return array
     */
    public static function prefilledFromOldInput()
    {
        $data = [
            'description' => Input::old('description'),
            'amount'      => floatval(Input::old('amount')),
            'pct'         => intval(Input::old('pct')),
            'dom'         => intval(Input::old('dom')),
            'inactive'    => intval(Input::old('inactive')) == 1 ? true : false,
            'account_id'  => intval(Input::old('account_id')),
        ];
        foreach (Type::allTypes() as $type) {
            $data[$type->type] = Input::old($type->type);
        }
        return $data;
    }

    /**
     * @param Transaction $transaction
     *
     * @return array
     */
    public static function prefilledFromTransaction(Transaction $transaction)
    {
        $data = [
            'description' => $transaction->description,
            'amount'      => floatval($transaction->amount),
            'dom'         => intval($transaction->date->format('d')),
            'pct'         => 10,
            'inactive'    => false,
            'account_id'  => $transaction->account_id
        ];
        foreach (Type::allTypes() as $type) {
            $data[$type->type] = $transaction->hasComponentOfType($type) ? $transaction->getComponentOfType($type) : '';
        }
        return $data;

    }

    /**
     * @param Predictable $predictable
     *
     * @return array
     */
    public static function prefilledFromPredictable(Predictable $predictable)
    {
        $data = [
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
        foreach (Type::allTypes() as $type) {
            $data[$type->type] = $predictable->hasComponentOfType($type) ? $predictable->getComponentOfType($type)->name : '';
        }
        return $data;
    }

}