<?php
use Carbon\Carbon as Carbon;

/**
 * Class Limit
 *
 * @property integer $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @property integer $component_id
 * @property float $amount
 * @property string $date
 * @property-read \Component $component
 * @method static Limit inMonth($date)
 */
class Limit extends Eloquent
{

    public static $rules
        = ['amount'       => 'required|numeric|min:0|max:66536',
           'component_id' => 'required|exists:components,id',];
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    protected $fillable = ['amount', 'component_id', 'date'];

    /**
     * Gets the component for this limit.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
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

    /**
     * All dates that need to be a Carbon model.
     *
     * @return array
     */
    public function getDates()
    {
        return ['created_at', 'updated_at', 'date', 'deleted_at'];
    }

    public function getAmountAttribute($value)
    {
        return floatval($value);
    }


}
