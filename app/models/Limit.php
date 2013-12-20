<?php
use Carbon\Carbon as Carbon;

/**
 * An Eloquent Model: 'limit'
 *
 * @property integer         $id
 * @property \Carbon\Carbon  $created_at
 * @property \Carbon\Carbon  $updated_at
 * @property \Carbon\Carbon  $deleted_at
 * @property integer         $component_id
 * @property float           $amount
 * @property string          $date
 * @property-read \Component $component
 * @method static limit inMonth($date)
 */
class Limit extends Eloquent
{

    public static $rules
        = ['amount'       => 'required|numeric|min:0.01|max:66536',
                'component_id' => 'required|exists:components,id',];
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    protected $fillable = ['amount', 'component_id', 'date'];

    public function component()
    {
        return $this->belongsTo('Component');
    }

    public function scopeInMonth($query, Carbon $date)
    {
        return $query->where(
            DB::Raw('DATE_FORMAT(`date`,"%m-%Y")'), '=', $date->format('m-Y')
        );
    }

    public function getDates()
    {
        return ['created_at', 'updated_at', 'date','deleted_at'];
    }


}
