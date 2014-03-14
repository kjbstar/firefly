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
 * @property boolean $inactive
 * @method static Predictable active()
 */
class Predictable extends Eloquent
{
    public static $rules
        = ['description' => 'required|between:1,500',
           'user_id'     => 'required|exists:users,id',
           'dom'         => 'required|numeric|between:1,31',
           'amount'      => 'required|numeric|not_in:0',
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
        foreach ($this->components as $component) {
            if ($component->type == 'beneficiary') {
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
        foreach ($this->components as $component) {
            if ($component->type == 'category') {
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
        foreach ($this->components as $component) {
            if ($component->type == 'budget') {
                return $component;
            }
        }

        return null;
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
     * Get the component name decrypted.
     *
     * @param $value
     *
     * @return string
     */
    public function getDescriptionAttribute($value)
    {
        if (is_null($value)) {
            return null;
        }

        return Crypt::decrypt($value);
    }

    /**
     * Encrypt the name while setting it.
     *
     * @param $value
     */
    public function setDescriptionAttribute($value)
    {
        if (strlen($value) > 0) {
            $this->attributes['description'] = Crypt::encrypt($value);
        } else {
            $this->attributes['description'] = null;
        }
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