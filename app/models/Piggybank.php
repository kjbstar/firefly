<?php

class Piggybank extends Eloquent
{

    public static $rules
        = ['name'   => 'required|between:1,50', 'amount' => 'required|numeric',
           'target' => 'numeric', 'user_id' => 'required|exists:users,id',];
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $fillable
        = ['name', 'amount', 'target', 'user_id'];


    public function user()
    {
        return $this->belongsTo('User');
    }

    public function getNameAttribute($value)
    {
        return Crypt::decrypt($value);
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = Crypt::encrypt($value);
    }

    public function pctFilled()
    {
        if (is_null($this->target)) {
            return 0;
        } else {
            $pct = round(($this->amount / $this->target) * 100);

            return $pct >= 100 ? 100 : $pct;
        }
    }
}