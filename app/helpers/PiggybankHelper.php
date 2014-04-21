<?php


/**
 * Class PiggybankHelper
 */
class PiggybankHelper
{

    public static function getOrders() {
        $max = Auth::user()->piggybanks()->max('order');
        $arr = [];
        for($i=1;$i<=$max;$i++) {
            $arr[$i] = '#'.$i;
        }
        return $arr;
    }

    /**
     * @return array
     */
    public static function emptyPrefilledAray()
    {
        return [
            'name'   => '',
            'target' => '',
            'amount' => '',
            'order' => ''
        ];
    }

    /**
     * @return array
     */
    public static function prefilledFromOldInput()
    {
        return [
            'name'   => Input::old('name'),
            'target' => floatval(Input::old('target')),
            'amount' => floatval(Input::old('amount')),
            'order' => intval(Input::old('order')),
        ];
    }

    /**
     * @param Piggybank $pig
     *
     * @return array
     */
    public static function prefilledFromPiggybank(Piggybank $pig)
    {
        return [
            'name'   => $pig->name,
            'target' => floatval($pig->target),
            'amount' => floatval($pig->amount),
            'order' => intval($pig->order),
        ];
    }
} 