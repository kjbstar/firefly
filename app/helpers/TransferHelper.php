<?php


/**
 * Class TransferHelper
 */
class TransferHelper
{
    /**
     * @return array
     */
    public static function emptyPrefilledAray()
    {
        return [
            'description'     => '',
            'amount'          => '',
            'date'            => date('Y-m-d'),
            'accountfrom_id'  => null,
            'accountto_id'    => null,
            'beneficiary'     => '',
            'category'        => '',
            'budget'          => '',
            'ignoreallowance' => false
        ];
    }

    /**
     * @return array
     */
    public static function prefilledFromOldInput()
    {
        return [
            'description'     => Input::old('description'),
            'amount'          => floatval(Input::old('amount')),
            'date'            => Input::old('date'),
            'accountfrom_id'  => intval(Input::old('accountfrom_id')),
            'accountto_id'    => intval(Input::old('accountto_id')),
            'beneficiary'     => Input::old('beneficiary'),
            'category'        => Input::old('category'),
            'budget'          => Input::old('budget'),
            'ignoreallowance' => intval(Input::old('ignoreallowance')) == 1 ? true : false,
        ];
    }

    /**
     * @param Transfer $transfer
     *
     * @return array
     */
    public static function prefilledFromTransfer(Transfer $transfer)
    {
        return [
            'description'    => $transfer->description,
            'amount'         => floatval($transfer->amount),
            'date'           => $transfer->date->format('Y-m-d'),
            'accountfrom_id' => $transfer->accountfrom_id,
            'accountto_id'   => $transfer->accountfrom_id,
            'beneficiary'    => !is_null($transfer->beneficiary) ? $transfer->beneficiary->name : null,
            'category'       => !is_null($transfer->category) ? $transfer->category->name : null,
            'budget'         => !is_null($transfer->budget) ? $transfer->budget->name : null,
            'ignoreallowance'  => intval($transfer->ignoreallowance) == 1 ? true : false,
        ];
    }

} 