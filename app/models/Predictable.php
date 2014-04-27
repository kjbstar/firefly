<?php

/**
 * An Eloquent Model: 'Predictable'
 *
 * @property integer                                                      $id
 * @property \Carbon\Carbon                                               $created_at
 * @property-read \User                                                   $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\Component[]   $components
 * @property \Carbon\Carbon                                               $updated_at
 * @property integer                                                      $user_id
 * @property string                                                       $description
 * @property float                                                        $amount
 * @property integer                                                      $dom
 * @property integer                                                      $pct
 * @property-read \Illuminate\Database\Eloquent\Collection|\Transaction[] $transactions
 * @property-read mixed                                                   $beneficiary
 * @property-read mixed                                                   $category
 * @property-read mixed                                                   $budget
 * @property boolean                                                      $inactive
 * @method static Predictable active()
 * @method static \Illuminate\Database\Query\Builder|\Predictable whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Predictable whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Predictable whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Predictable whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\Predictable whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\Predictable whereAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\Predictable whereDom($value)
 * @method static \Illuminate\Database\Query\Builder|\Predictable wherePct($value)
 * @method static \Illuminate\Database\Query\Builder|\Predictable whereInactive($value)
 * @property integer                                                      $account_id
 * @property-read \Account                                                $account
 * @method static \Illuminate\Database\Query\Builder|\Predictable whereAccountId($value)
 */
class Predictable extends Eloquent
{
    public static $rules
        = ['description' => 'required|between:1,500',
           'user_id'     => 'required|exists:users,id',
           'dom'         => 'required|numeric|between:1,31',
           'amount'      => 'required|numeric|not_in:0',
           'account_id'  => 'required|exists:accounts,id',
           'inactive'    => 'required|numeric|between:0,1'];
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $fillable
        = ['description', 'inactive', 'user_id', 'dom', 'amount'];

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
     * Get the beneficiary.
     *
     * @return Component|null
     */
    public function getBeneficiaryAttribute()
    {

        $key = $this->id.'-predictable-beneficiary';
        if(Cache::has($key)) {
            return Cache::get($key);
        }
        foreach ($this->components as $component) {
            if ($component->type == 'beneficiary') {
                Cache::forever($key,$component);
                return $component;
            }
        }

        return null;

    }

    /**
     * Get the category
     *
     * @return Component|null
     */
    public function getCategoryAttribute()
    {
        $key = $this->id . '-predictable-category';
        if (Cache::has($key)) {
            return Cache::get($key);
        }
        foreach ($this->components as $component) {
            if ($component->type == 'category') {
                Cache::forever($key, $component);
                return $component;
            }
        }

        return null;

    }

    /**
     * Get the budget
     *
     * @return Component|null
     */
    public function getBudgetAttribute()
    {
        $key = $this->id . '-predictable-budget';
        if (Cache::has($key)) {
            return Cache::get($key);
        }
        foreach ($this->components as $component) {
            if ($component->type == 'budget') {
                Cache::forever($key, $component);
                return $component;
            }
        }

        return null;
    }

    /**
     * To get the account from attribute, we use this
     * caching function.
     * @return mixed
     */
    public function getAccountAttribute() {
        $key = $this->id.'-predictable-account';
        if(Cache::has($key)) {
            return Cache::get($key);
        }
        $var = $this->account()->first();
        Cache::forever($key,$var);
        return $var;
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
}