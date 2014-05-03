<?php

/**
 * Class PredictableHelper
 */
class PredictableHelper
{
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
            'account_id'  => $predictable->account_id
        ];
        foreach (Type::allTypes() as $type) {
            $data[$type->type] = $predictable->hasComponentOfType($type) ? $predictable->getComponentOfType($type)->name
                : '';
        }
        return $data;
    }

}