<?php
use Carbon\Carbon as Carbon;

/**
 * An Eloquent Model: 'Balancemodifier'
 *
 * @property integer        $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property integer        $account_id
 * @property string         $date
 * @property float          $balance
 * @property string         $balance_encrypted
 * @property-read \Account  $account
 */
class Balancemodifier extends Eloquent
{

    public function account()
    {
        return $this->belongsTo('Account');
    }

    public function getDates()
    {
        return ['created_at', 'updated_at', 'date'];
    }

    public function scopeOnDay($query, Carbon $date)
    {
        return $query->where(
            'date', '=', $date->format('Y-m-d')
        );
    }

    public function scopeBeforeDay($query, Carbon $date)
    {
        return $query->where(
            'date', '<', $date->format('Y-m-d')
        );
    }

}
