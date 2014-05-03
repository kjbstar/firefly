<?php


/**
 * Class TransactionHelper
 */
class TransactionHelper
{
    /**
     * @return array
     */
    public static function emptyPrefilledAray()
    {
        $data = [
            'description'      => '',
            'amount'           => '',
            'date'             => date('Y-m-d'),
            'account_id'       => null,
            'ignoreprediction' => 0,
            'ignoreallowance'  => 0,
            'mark'             => 0
        ];
        foreach (Type::allTypes() as $type) {
            $data[$type->type] = '';
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
        $dayOfMonth = sprintf('%02d', $predictable->dom);
        $data = [
            'description'      => $predictable->description,
            'amount'           => $predictable->amount,
            'date'             => date('Y-m-') . $dayOfMonth,
            'account_id'       => $predictable->account_id,
            'ignoreprediction' => 0,
            'ignoreallowance'  => 0,
            'mark'             => 0
        ];
        foreach (Type::allTypes() as $type) {
            $t = $type->type;
            $data[$t] = $predictable->hasComponentOfType($type) ? $predictable->getComponentOfType($type)->name : '';
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
            'description'      => $transaction->description,
            'amount'           => $transaction->amount,
            'date'             => $transaction->date->format('Y-m-d'),
            'account_id'       => $transaction->account_id,
            'ignoreprediction' => intval($transaction->ignoreprediction) == 1 ? true : false,
            'ignoreallowance'  => intval($transaction->ignoreallowance) == 1 ? true : false,
            'mark'             => intval($transaction->mark) == 1 ? true : false,
        ];
        foreach (Type::allTypes() as $type) {
            $t = $type->type;
            $data[$t] = $transaction->hasComponentOfType($type) ? $transaction->getComponentOfType($type)->name : '';
        }
        return $data;

    }

    /**
     * @return array
     */
    public static function prefilledFromOldInput()
    {
        $data = ['description'      => Input::old('description'),
                 'amount'           => floatval(Input::old('amount')),
                 'date'             => Input::old('date'),
                 'account_id'       => intval(Input::old('account_id')),
                 'ignoreprediction' => intval(Input::old('ignoreprediction')) == 1 ? true : false,
                 'ignoreallowance'  => intval(Input::old('ignoreallowance')) == 1 ? true : false,
                 'mark'             => intval(Input::old('mark')) == 1 ? true : false
        ];
        foreach (Type::allTypes() as $type) {
            $t = $type->type;
            $data[$t] = Input::old($t);
        }
        return $data;
    }

}