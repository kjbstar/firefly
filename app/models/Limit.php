<?php
use Carbon\Carbon as Carbon;

/**
 * Limit
 *
 * @property integer         $id
 * @property \Carbon\Carbon  $created_at
 * @property \Carbon\Carbon  $updated_at
 * @property integer         $component_id
 * @property float           $amount
 * @property \Carbon\Carbon  $date
 * @property-read \Component $component
 * @method static \Limit inMonth($date)
 * @property integer $account_id
 * @property-read \Account $account
 */
class Limit extends Eloquent
{

    public static $rules
        = [
            'amount'       => 'required|numeric|min:0.01|max:66536',
            'component_id' => 'required|exists:components,id',
            'account_id' => 'exists:accounts,id',
        ];
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $fillable = ['amount', 'component_id', 'date','account_id'];

    /**
     * Gets the component for this limit.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function component()
    {
        return $this->belongsTo('Component');
    }

    /**
     * Gets the account for this limit.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('Account');
    }

    /**
     * @param        $query
     * @param Carbon $date
     *
     * @return mixed
     */
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
        return ['created_at', 'updated_at', 'date'];
    }

    /**
     * @param $value
     *
     * @return float
     */
    public function getAmountAttribute($value)
    {
        return floatval($value);
    }


}
