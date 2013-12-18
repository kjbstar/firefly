<?php

use Carbon\Carbon as Carbon;

/**
 * An Eloquent Model: 'Transfer'
 *
 * @property integer        $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property integer        $user_id
 * @property integer        $accountfrom_id
 * @property integer        $accountto_id
 * @property string         $description
 * @property float          $amount
 * @property string         $date
 * @property-read \Account  $accountfrom
 * @property-read \Account  $accountto
 * @property-read \User     $user
 * @method static Transfer inMonth($date)
 */
class Transfer extends Eloquent
{

    public static $rules
        = ['user_id'        => 'required|exists:users,id',
           'description'    => 'required|between:1,500',
           'date'           => 'required|before:2038-01-01|after:1980-01-01',
           'amount'         => 'required|numeric|between:0.01,65536',
           'accountfrom_id' => 'required|integer|exists:accounts,id|different:accountto_id',
           'accountto_id'   => 'required|integer|exists:accounts,id',];
    protected $fillable
        = ['date', 'amount', 'description', 'accountfrom_id', 'accountto_id',
           'user_id'];
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function accountfrom()
    {
        return $this->belongsTo('Account', 'accountfrom_id');
    }

    public function accountto()
    {
        return $this->belongsTo('Account', 'accountto_id');
    }

    public function user()
    {
        return $this->belongsTo('User');
    }

    public function scopeInMonth($query, Carbon $date)
    {
        return $query->where(
            DB::Raw('DATE_FORMAT(`date`,"%m-%Y")'), '=', $date->format('m-Y')
        );
    }

    public function getDescriptionAttribute($value)
    {
        return Crypt::decrypt($value);
    }

    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = Crypt::encrypt($value);
    }

    public function getDateAttribute($value)
    {
        return new Carbon($value);
    }

    public function setDateAttribute($value)
    {
        if ($value instanceof Carbon) {
            $this->attributes['date'] = $value->format('Y-m-d');
        } else {
            $this->attributes['date'] = $value;
        }
    }

}
