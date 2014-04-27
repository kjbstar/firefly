<?php


class Type extends Eloquent
{
    public static $rules
        = [
            'type' => 'required|between:1,40',
        ];
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $fillable = ['type'];

    public function components()
    {
        return $this->hasMany('Component');
    }
} 