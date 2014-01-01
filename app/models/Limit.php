<?php
use Carbon\Carbon as Carbon;

/**
 * Class Limit
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

    /**
     * Makes the scope for the month given in $date
     *
     * @param        $query The query
     * @param Carbon $date  The date
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
        return ['created_at', 'updated_at', 'date', 'deleted_at'];
    }


}
