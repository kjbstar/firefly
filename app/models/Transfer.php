<?php

use Carbon\Carbon as Carbon;


/**
 * Transfer
 *
 * @property integer $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property integer $user_id
 * @property integer $accountfrom_id
 * @property integer $accountto_id
 * @property string $description
 * @property float $amount
 * @property \Carbon\Carbon $date
 * @property boolean $ignoreallowance
 * @property-read \Account $accountfrom
 * @property-read \Account $accountto
 * @property-read \Illuminate\Database\Eloquent\Collection|\Component[] $components
 * @property-read \User $user
 * @method static \Transfer inMonth($date)
 * @method static \Transfer inYear($date)
 * @method static \Transfer afterDate($date)
 * @method static \Transfer beforeDate($date)
 */
class Transfer extends ComponentEnabledModel
{

    public static $rules
        = ['user_id'         => 'required|exists:users,id',
           'description'     => 'required|between:1,255',
           'date'            => 'required|before:2038-01-01|after:1980-01-01',
           'amount'          => 'required|numeric|between:0.01,65536',
           'accountfrom_id'  => 'required|integer|exists:accounts,id|different:accountto_id',
           'accountto_id'    => 'required|integer|exists:accounts,id',
           'ignoreallowance' => 'required|numeric|between:0,1',

        ];
    protected $fillable
        = ['date', 'amount', 'description', 'accountfrom_id', 'accountto_id',
           'user_id', 'ignoreallowance'];
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Which account did the transfer come from?
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function accountfrom()
    {
        return $this->belongsTo('Account', 'accountfrom_id');
    }


    /**
     * What account is the transfer going to?
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function accountto()
    {
        return $this->belongsTo('Account', 'accountto_id');
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
     * Which user does this transfer belong to?
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('User');
    }

    /**
     * Tighten the scope of the query to a certain month.
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
     * Get all date fields that must be Carbon objects.
     *
     * @return array
     */
    public function getDates()
    {
        return ['created_at', 'updated_at', 'date'];
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

}
