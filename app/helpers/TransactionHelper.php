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
        return [
            'description'      => '',
            'amount'           => '',
            'date'             => date('Y-m-d'),
            'account_id'       => null,
            'beneficiary'      => '',
            'category'         => '',
            'budget'           => '',
            'ignoreprediction' => 0,
            'ignoreallowance'  => 0,
            'mark'             => 0
        ];
    }

    /**
     * @param Predictable $predictable
     *
     * @return array
     */
    public static function prefilledFromPredictable(Predictable $predictable)
    {
        $dayOfMonth = sprintf('%02d', $predictable->dom);
        return [
            'description'      => $predictable->description,
            'amount'           => $predictable->amount,
            'date'             => date('Y-m-') . $dayOfMonth,
            'account_id'       => null,
            'beneficiary'      => is_null($predictable->beneficiary) ? '' : $predictable->beneficiary->name,
            'category'         => is_null($predictable->category) ? '' : $predictable->category->name,
            'budget'           => is_null($predictable->budget) ? '' : $predictable->budget->name,
            'ignoreprediction' => 0,
            'ignoreallowance'  => 0,
            'mark'             => 0
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
            'description'      => $transaction->description,
            'amount'           => $transaction->amount,
            'date'             => $transaction->date->format('Y-m-d'),
            'account_id'       => $transaction->account_id,
            'beneficiary'      => is_null($transaction->beneficiary) ? '' : $transaction->beneficiary->name,
            'category'         => is_null($transaction->category) ? '' : $transaction->category->name,
            'budget'           => is_null($transaction->budget) ? '' : $transaction->budget->name,
            'ignoreprediction' => intval($transaction->ignoreprediction) == 1 ? true : false,
            'ignoreallowance'  => intval($transaction->ignoreallowance) == 1 ? true : false,
            'mark'             => intval($transaction->mark) == 1 ? true : false,
        ];
    }

    /**
     * @return array
     */
    public static function prefilledFromOldInput()
    {
        return ['description'      => Input::old('description'),
                'amount'           => floatval(Input::old('amount')),
                'date'             => Input::old('date'),
                'account_id'       => intval(Input::old('account_id')),
                'beneficiary'      => intval(Input::old('beneficiary_id')),
                'category'         => intval(Input::old('category_id')),
                'budget'           => intval(Input::old('budget_id')),
                'ignoreprediction' => intval(Input::old('ignoreprediction')),
                'ignoreallowance'  => intval(Input::old('ignoreallowance')),
                'mark'             => intval(Input::old('mark'))

        ];
    }

}