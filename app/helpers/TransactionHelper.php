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
        foreach (Type::get() as $type) {
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
            'account_id'       => null,
            'ignoreprediction' => 0,
            'ignoreallowance'  => 0,
            'mark'             => 0
        ];
        foreach (Type::get() as $type) {
            $t = $type->type;
            $data[$t] = is_null($predictable->$t) ? '' : $predictable->$t->name;
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
//        'beneficiary'      => is_null($transaction->beneficiary) ? '' : $transaction->beneficiary->name,
//            'category'         => is_null($transaction->category) ? '' : $transaction->category->name,
//            'budget'           => is_null($transaction->budget) ? '' : $transaction->budget->name,
        foreach (Type::get() as $type) {
            $t = $type->type;
            $data[$t] = is_null($transaction->$t) ? '' : $transaction->$t->name;
        }
        return $data;

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
                'ignoreprediction' => intval(Input::old('ignoreprediction')) == 1 ? true : false,
                'ignoreallowance'  => intval(Input::old('ignoreallowance')) == 1 ? true : false,
                'mark'             => intval(Input::old('mark')) == 1 ? true : false

        ];
    }

}