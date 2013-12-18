<?php

use Carbon\Carbon as Carbon;

/**
 * An Eloquent Model: 'Transaction'
 *
 * @property integer                                                    $id
 * @property integer                                                    $user_id
 * @property integer                                                    $account_id
 * @property \Carbon\Carbon                                             $created_at
 * @property \Carbon\Carbon                                             $updated_at
 * @property string                                                     $description
 * @property float                                                      $amount
 * @property string                                                     $date
 * @property boolean                                                    $ignore
 * @property boolean                                                    $mark
 * @property integer                                                    $beneficiary_idX
 * @property integer                                                    $budget_idX
 * @property integer                                                    $category_idX
 * @property boolean                                                    $assigned
 * @property-read \Account                                              $account
 * @property-read \Illuminate\Database\Eloquent\Collection|\Component[] $components
 * @property-read \User                                                 $user
 * @method static Transaction inMonth($date)
 * @method static Transaction onDay($date)
 * @method static Transaction onDayOfMonth($date)
 * @method static Transaction betweenDates($start, $end)
 * @method static Transaction expenses()
 * @method static Transaction incomes()
 */
class Transaction extends Eloquent
{

    public static $rules
        = ['user_id'     => 'required|exists:users,id|numeric',
                'account_id'  => 'required|integer|exists:accounts,id',
                'date'        => 'required|before:2038-01-01|after:1980-01-01',
                'description' => 'required|between:1,500',
                'amount'      => 'required|numeric|between:-65536,65536|not_in:0',
                'ignore'      => 'required|numeric|between:0,1'];
    protected $guarded = ['id', 'created_at', 'updated_at'];
    private $_beneficiary;
    private $_budget;
    private $_category;

    public function account()
    {
        return $this->belongsTo('Account');
    }

    public function beneficiary()
    {
        return $this->_getComponent('beneficiary');
    }

    public function addComponent(Component $c = null)
    {
        if (is_null($c)) {
            return;
        }
        $this->components()->attach($c->id);
    }

    public function hasBudget()
    {
        return $this->hasComponent('budget');
    }

    public function hasBeneficiary()
    {
        return $this->hasComponent('beneficiary');
    }

    public function hasCategory()
    {
        return $this->hasComponent('category');
    }

    private function hasComponent($type)
    {
        foreach ($this->components()->get() as $comp) {
            if ($comp->type === $type) {
                return true;
            }
        }

        return false;
    }

    private function _getComponent($type)
    {
        $var = '_' . $type;
        if (is_null($this->$var)) {
            $this->$var = $this->components()->where('type', $type)->first();
        }

        return $this->$var;
    }

    public function components()
    {
        return $this->belongsToMany('Component');
    }

    public function category()
    {
        return $this->_getComponent('category');
    }

    public function budget()
    {
        return $this->_getComponent('budget');
    }

    public function scopeInMonth($query, Carbon $date)
    {
        return $query->where(
            DB::Raw('DATE_FORMAT(`date`,"%m-%Y")'), '=', $date->format('m-Y')
        );
    }

    public function scopeOnDay($query, Carbon $date)
    {
        return $query->where(
            'date', '=', $date->format('Y-m-d')
        );
    }

    public function scopeOnDayOfMonth($query, Carbon $date)
    {
        return $query->where(
            DB::Raw('DATE_FORMAT(`date`,"%d")'), '=', $date->format('d')
        );
    }

    public function scopeBetweenDates($query, Carbon $start, Carbon $end)
    {
        return $query->where(
            'date', '>=', $start->format('Y-m-d')
        )->where(
                'date', '<=', $end->format('Y-m-d')
            );
    }

    public function scopeExpenses($query)
    {
        return $query->where('amount', '<', 0.0);
    }

    public function scopeHasComponent($query, $component)
    {
        return $query->with(
            ['components' => function ($query) use ($component) {
                    $query->where('components.type', $component);
                }]
        );
    }

    public function scopeIncomes($query)
    {
        return $query->where('amount', '>', 0.0);
    }

    public function user()
    {
        return $this->belongsTo('User');
    }

    public function getDescriptionAttribute($value)
    {
        return is_null($value) ? null : Crypt::decrypt($value);
    }

    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = Crypt::encrypt($value);
    }

    public function getDateAttribute($value)
    {
        return new Carbon($value);
    }

    public function setDateAttribute($value)
    {
        if ($value instanceof Carbon) {
            $this->attributes['date'] = $value->format('Y-m-d');
        } else {
            $this->attributes['date'] = $value;
        }
    }

}
