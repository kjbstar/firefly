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
        $data = [
            'description'     => '',
            'amount'          => '',
            'date'            => date('Y-m-d'),
            'accountfrom_id'  => null,
            'accountto_id'    => null,
            'ignoreallowance' => false
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
            'description'     => Input::old('description'),
            'amount'          => floatval(Input::old('amount')),
            'date'            => Input::old('date'),
            'accountfrom_id'  => intval(Input::old('accountfrom_id')),
            'accountto_id'    => intval(Input::old('accountto_id')),
            'ignoreallowance' => intval(Input::old('ignoreallowance')) == 1 ? true : false,
        ];
        foreach (Type::allTypes() as $type) {
            $data[$type->type] = Input::old($type->type);
        }
        return $data;
    }

    /**
     * @param Transfer $transfer
     *
     * @return array
     */
    public static function prefilledFromTransfer(Transfer $transfer)
    {
        $data = [
            'description'     => $transfer->description,
            'amount'          => floatval($transfer->amount),
            'date'            => $transfer->date->format('Y-m-d'),
            'accountfrom_id'  => $transfer->accountfrom_id,
            'accountto_id'    => $transfer->accountfrom_id,
            'ignoreallowance' => intval($transfer->ignoreallowance) == 1 ? true : false,
        ];
        foreach (Type::allTypes() as $type) {
            $data[$type->type] = $transfer->hasComponentOfType($type) ? $transfer->getComponentOfType($type)->name : '';
        }
        return $data;
    }

} 