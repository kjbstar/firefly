<?php


class PiggybankHelper
{


    public static function emptyPrefilledAray()
    {
        return [
            'name'   => '',
            'target' => '',
            'amount' => '',
        ];
    }

    public static function prefilledFromOldInput()
    {
        return [
            'name'   => Input::old('name'),
            'target' => intval(Input::old('target')),
            'amount' => intval(Input::old('amount')),
        ];
    }

    public static function prefilledFromPiggybank(Piggybank $pig)
    {
        return [
            'name'   => $pig->name,
            'target' => intval($pig->target),
            'amount' => intval($pig->amount),
        ];
    }
} 