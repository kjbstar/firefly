<?php

use Carbon\Carbon as Carbon;


/**
 * Transaction
 *
 * @property integer $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property integer $user_id
 * @property integer $account_id
 * @property integer $predictable_id
 * @property string $description
 * @property float $amount
 * @property \Carbon\Carbon $date
 * @property boolean $ignoreprediction
 * @property boolean $ignoreallowance
 * @property boolean $mark
 * @property-read \Account $account
 * @property-read \Illuminate\Database\Eloquent\Collection|\Component[] $components
 * @property-read \User $user
 * @property-read \Predictable $predictable
 * @method static \Transaction inMonth($date) 
 * @method static \Transaction inYear($date) 
 * @method static \Transaction onDay($date) 
 * @method static \Transaction betweenDates($start, $end) 
 * @method static \Transaction expenses() 
 * @method static \Transaction afterDate($date) 
 * @method static \Transaction beforeDate($date) 
 * @method static \Transaction incomes() 
 */
class Transaction extends ComponentEnabledModel
{

    public static $rules
        = ['user_id'          => 'required|exists:users,id|numeric',
           'account_id'       => 'required|integer|exists:accounts,id',
           'date'             => 'required|before:2038-01-01|after:1980-01-01',
           'description'      => 'required|between:1,255',
           'amount'           => 'required|numeric|between:-65536,65536|not_in:0',
           'ignoreprediction' => 'required|numeric|between:0,1',
           'ignoreallowance'  => 'required|numeric|between:0,1',
           'mark'             => 'required|numeric|between:0,1'

        ];
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $fillable = ['user_id', 'date', 'description', 'amount', 'ignoreprediction', 'ignoreallowance', 'mark'];

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
     * Get all components belonging to this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function components()
    {
        return $this->belongsToMany('Component');
    }

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
     * Limits the scope to a certain year.
     *
     * @param        $query
     * @param Carbon $date
     *
     * @return mixed
     */
    public function scopeInYear($query, Carbon $date)
    {
        return $query->where(
            DB::Raw('DATE_FORMAT(`date`,"%Y")'), '=', $date->format('Y')
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
     * @param        $query
     * @param Carbon $date
     *
     * @return mixed
     */
    public function scopeAfterDate($query, Carbon $date)
    {
        return $query->where(
            'date', '>=', $date->format('Y-m-d')
        );
    }

    /**
     * @param        $query
     * @param Carbon $date
     *
     * @return mixed
     */
    public function scopeBeforeDate($query, Carbon $date)
    {
        return $query->where(
            'date', '<=', $date->format('Y-m-d')
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
     * These date/time fields must be Carbon objects.
     *
     * @return array
     */
    public function getDates()
    {
        return ['created_at', 'updated_at', 'date'];
    }
}
