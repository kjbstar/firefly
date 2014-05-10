<?php
use Carbon\Carbon as Carbon;
/**
 * Predictable
 *
 * @property integer $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property integer $user_id
 * @property string $description
 * @property float $amount
 * @property integer $dom
 * @property integer $pct
 * @property boolean $inactive
 * @property integer $account_id
 * @property-read \User $user
 * @property-read \Account $account
 * @property-read \Illuminate\Database\Eloquent\Collection|\Component[] $components
 * @property-read \Illuminate\Database\Eloquent\Collection|\Transaction[] $transactions
 * @method static \Predictable active()
 */
class Predictable extends ComponentEnabledModel
{
    public static $rules
        = ['description' => 'required|between:1,255',
           'user_id'     => 'required|exists:users,id',
           'dom'         => 'required|numeric|between:1,31',
           'amount'      => 'required|numeric|not_in:0',
           'account_id'  => 'required|exists:accounts,id',
           'inactive'    => 'required|numeric|between:0,1'];
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $fillable
        = ['description', 'inactive', 'account_id','user_id', 'dom', 'amount'];

    /**
     * Return the user this Predictable belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('User');
    }

    public function account()
    {
        return $this->belongsTo('Account');
    }

    /**
     * Limits the scope to a active predictables.
     *
     * @param        $query
     *
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->where('inactive', 0);
    }


    /**
     * Get all components belonging to this predictable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function components()
    {
        return $this->belongsToMany('Component');
    }

    /**
     * Account has transactions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany('Transaction');
    }

    /**
     * These date/time fields must be Carbon objects.
     *
     * @return array
     */
    public function getDates()
    {
        return ['created_at', 'updated_at'];
    }

    public function minimumAmount()
    {
        $pct = (100 - $this->pct) / 100;
        return $this->amount * $pct;
    }

    public function maximumAmount()
    {
        $pct = (100 + $this->pct) / 100;
        return $this->amount * $pct;
    }

    public function dayOfMonth() {
        $date = new Carbon($this->dom.'-01-2012');
        return $date->format('jS');
    }
}