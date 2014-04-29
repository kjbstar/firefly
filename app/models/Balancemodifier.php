<?php
use Carbon\Carbon as Carbon;

/**
 * Balancemodifier
 *
 * @property integer $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property integer $account_id
 * @property \Carbon\Carbon $date
 * @property float $balance
 * @property-read \Account $account
 * @method static \Balancemodifier onDay($date) 
 * @method static \Balancemodifier beforeDate($date) 
 */
class Balancemodifier extends Eloquent
{

    /**
     * Get the account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('Account');
    }

    /**
     * Get the date fields that need to be a Carbon object.
     *
     * @return array
     */
    public function getDates()
    {
        return ['created_at', 'updated_at', 'date'];
    }

    /**
     * Scopes a search to a certain date.
     *
     * @param        $query
     * @param Carbon $date
     *
     * @return mixed
     */
    public function scopeOnDay($query, Carbon $date)
    {
        return $query->where(
            'date', '=', $date->format('Y-m-d')
        );
    }

    /**
     * Scopes a search to before a date.
     *
     * @param        $query
     * @param Carbon $date
     *
     * @return mixed
     */
    public function scopeBeforeDate($query, Carbon $date)
    {
        return $query->where(
            'date', '<', $date->format('Y-m-d')
        );
    }

}
