<?php

use Carbon\Carbon as Carbon;

/**
 * Class Transaction
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $account_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $description
 * @property float $amount
 * @property string $date
 * @property boolean $ignoreprediction
 * @property boolean $ignoreallowance
 * @property boolean $mark
 * @property-read mixed $beneficiary
 * @property-read mixed $category
 * @property-read mixed $budget
 * @property-read \Account $account
 * @property-read \Illuminate\Database\Eloquent\Collection|\Component[] $components
 * @property-read \User $user
 * @method static Transaction inMonth($date)
 * @method static Transaction onDay($date)
 * @method static Transaction onDayOfMonth($date)
 * @method static Transaction betweenDates($start, $end)
 * @method static Transaction expenses()
 * @method static Transaction hasComponentType($component)
 * @method static Transaction hasComponent($component)
 * @method static Transaction withLimitInMonth($date)
 * @method static Transaction inYear($date)
 * @method static Transaction afterDate($date)
 * @method static Transaction incomes()
 * @property integer $predictable_id
 * @property-read \Predictable $predictable
 * @method static Transaction beforeDate($date) 
 * @method static Transaction fromAccount($account) 
 */
class Transaction extends Eloquent
{

    public static $rules
        = ['user_id'          => 'required|exists:users,id|numeric',
           'account_id'       => 'required|integer|exists:accounts,id',
           'date'             => 'required|before:2038-01-01|after:1980-01-01',
           'description'      => 'required|between:1,500',
           'amount'           => 'required|numeric|between:-65536,65536|not_in:0',
           'ignoreprediction' => 'required|numeric|between:0,1',
           'ignoreallowance'  => 'required|numeric|between:0,1'];
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $appends = ['beneficiary', 'category', 'budget'];

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
     * Which account does this transaction belong to?
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('Account');
    }

    /**
     * "Fake" method to get the beneficiary.
     * TODO this process must be optimized.
     *
     * @return mixed
     */
//    public function beneficiary()
//    {
//        return $this->_getComponent('beneficiary');
//    }

    /**
     * Returns the object of $type (Component) if there is any.
     * TODO optimize, because the current three objects might be extended.
     *
     * @param $type
     *
     * @return mixed
     */
//    private function _getComponent($type)
//    {
//        $var = '_' . $type;
//        if (is_null($this->$var)) {
//            $this->$var = $this->components()->where('type', $type)->first();
//        }
//
//        return $this->$var;
//    }

    /**
     * Add the component to the transaction.
     *
     * @param Component $component
     */
    public function attachComponent(Component $component = null)
    {
        if (is_null($component)) {
            return;
        }
        $this->components()->attach($component->id);
    }

    /**
     * Get all components belonging to this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function components()
    {
        return $this->belongsToMany('Component');
    }

    /**
     * Returns true when the transaction is bound to a budget.
     *
     * @return bool
     */
//    public function hasBudget()
//    {
//        return $this->hasComponent('budget');
//    }

    /**
     * Returns true if this transaction has a beneficiary
     * TODO better structure.
     *
     * @return bool
     */
//    public function hasBeneficiary()
//    {
//        return $this->hasComponent('beneficiary');
//    }

    /**
     * Returns true if the transaction has a category
     * TODO fix this.
     *
     * @return bool
     */
//    public function hasCategory()
//    {
//        return $this->hasComponent('category');
//    }

    /**
     * Get the component of type 'category'
     *
     * @return mixed
     */
//    public function category()
//    {
//        return $this->_getComponent('category');
//    }

    /**
     * Get the component of type 'budget'
     *
     * @return mixed
     */
//    public function budget()
//    {
//        return $this->_getComponent('budget');
//    }

    /**
     * Limits the scope to a certain month.
     *
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
     * Limits the scope to a certain day.
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
     * Limits the scope to a day in the month.
     *
     * @param        $query
     * @param Carbon $date
     *
     * @return mixed
     */
    public function scopeOnDayOfMonth($query, Carbon $date)
    {
        return $query->where(
            DB::Raw('DATE_FORMAT(`date`,"%d")'), '=', $date->format('d')
        );
    }

    /**
     * Limits the scope to between two dates.
     *
     * @param        $query
     * @param Carbon $start
     * @param Carbon $end
     *
     * @return mixed
     */
    public function scopeBetweenDates($query, Carbon $start, Carbon $end)
    {
        return $query->where(
            'date', '>=', $start->format('Y-m-d')
        )->where(
                'date', '<=', $end->format('Y-m-d')
            );
    }

    /**
     * Limits the scope to only expenses.
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeExpenses($query)
    {
        return $query->where('amount', '<', 0.0);
    }

    /**
     * Limits the scope to only transactions with a component of type
     * $component.
     *
     * @param $query
     * @param $component
     *
     * @return mixed
     */
    public function scopeHasComponentType($query, $component)
    {
        return $query->with(
            ['components' => function ($query) use ($component) {
                    $query->where('components.type', $component);
                }]
        );
    }

    /**
     * Limits the scope to only transactions with a component of type
     * $component.
     *
     * @param $query
     * @param $component
     *
     * @return mixed
     */
    public function scopeHasComponent($query, Component $component)
    {
        return $query->with(
            ['components' => function ($query) use ($component) {
                    $query->where('components.id', $component->id);
                }]
        );
    }

    public function scopeWithLimitInMonth($query, Carbon $date)
    {
        return $query->with(
            ['components.limits' => function ($query) use ($date) {
                    return $query->inMonth($date);
                }]
        );

    }

    /**
     * Scope for a full year
     *
     * @param        $query
     * @param Carbon $date
     *
     * @return mixed
     */
    public function scopeInYear($query, Carbon $date)
    {
        return $query->where(
            'date', '>=', $date->format('Y') . '-01-01'
        )->where(
                'date', '<=', $date->format('Y') . '-12-31'
            );
    }

    public function scopeAfterDate($query, Carbon $date)
    {
        return $query->where(
            'date', '>=', $date->format('Y-m-d')
        );
    }

    public function scopeBeforeDate($query, Carbon $date)
    {
        return $query->where(
            'date', '<', $date->format('Y-m-d')
        );
    }

    /**
     * Limits the scope to incomes only.
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeIncomes($query)
    {
        return $query->where('amount', '>', 0.0);
    }

    /**
     * Limits the scope to incomes only.
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeFromAccount($query, Account $account)
    {
        return $query->where('account_id', '=', $account->id);
    }

    /**
     * Returns the user this transaction belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('User');
    }

    /**
     * Returns the predictable this transaction belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function predictable()
    {
        return $this->belongsTo('Predictable');
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
        if(is_null($value)) {
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
     * Set the ignoreprediction value no matter its input:
     *
     * @param $value
     */
    public function setIgnorepredictionAttribute($value)
    {
        if ($value == 1 || $value == '1' || $value == true
            || $value == 'true'
        ) {
            $this->attributes['ignoreprediction'] = 1;
        } else {
            $this->attributes['ignoreprediction'] = 0;
        }

    }

    /**
     * Set the ignoreallowance value no matter its input:
     *
     * @param $value
     */
    public function setIgnoreallowanceAttribute($value)
    {
        if ($value == 1 || $value == '1' || $value == true
            || $value == 'true'
        ) {
            $this->attributes['ignoreallowance'] = 1;
        } else {
            $this->attributes['ignoreallowance'] = 0;
        }

    }

    /**
     * Set the ignoreallowance value no matter its input:
     *
     * @param $value
     */
    public function setMarkAttribute($value)
    {
        if ($value == 1 || $value == '1' || $value == true
            || $value == 'true'
        ) {
            $this->attributes['mark'] = 1;
        } else {
            $this->attributes['mark'] = 0;
        }

    }

    /**
     * These date/time fields must be Carbon objects.
     *
     * @return array
     */
    public function getDates()
    {
        return ['created_at', 'updated_at', 'date'];
    }

    /**
     * Find component of type X.
     *
     * @param $type
     *
     * @return Component|null
     */
    public function getComponentByType($type)
    {
        foreach ($this->components as $component) {
            if ($component->type == $type) {
                return $component;
            }
        }

        return null;
    }

    /**
     * Check if the transaction has a component of type $type.
     *
     * @param $type
     *
     * @return bool
     */
//    private function hasComponent($type)
//    {
//        foreach ($this->components()->get() as $comp) {
//            if ($comp->type === $type) {
//                return true;
//            }
//        }
//
//        return false;
//    }
}
