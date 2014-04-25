<?php

use Carbon\Carbon as Carbon;

/**
 * Class Transfer
 *
 * @property integer                                                    $id
 * @property \Carbon\Carbon                                             $created_at
 * @property \Carbon\Carbon                                             $updated_at
 * @property integer                                                    $user_id
 * @property integer                                                    $accountfrom_id
 * @property integer                                                    $accountto_id
 * @property string                                                     $description
 * @property float                                                      $amount
 * @property string                                                     $date
 * @property-read \Account                                              $accountfrom
 * @property-read \Account                                              $accountto
 * @property-read \User                                                 $user
 * @method static Transfer inMonth($date)
 * @property-read \Illuminate\Database\Eloquent\Collection|\Component[] $components
 * @property-read mixed                                                 $beneficiary
 * @property-read mixed                                                 $category
 * @property-read mixed                                                 $budget
 * @method static \Illuminate\Database\Query\Builder|\Transfer whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Transfer whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Transfer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Transfer whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\Transfer whereAccountfromId($value)
 * @method static \Illuminate\Database\Query\Builder|\Transfer whereAccounttoId($value)
 * @method static \Illuminate\Database\Query\Builder|\Transfer whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\Transfer whereAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\Transfer whereDate($value)
 */
class Transfer extends Eloquent
{

    public static $rules
        = ['user_id'         => 'required|exists:users,id',
           'description'     => 'required|between:1,500',
           'date'            => 'required|before:2038-01-01|after:1980-01-01',
           'amount'          => 'required|numeric|between:0.01,65536',
           'accountfrom_id'  => 'required|integer|exists:accounts,id|different:accountto_id',
           'accountto_id'    => 'required|integer|exists:accounts,id',
           'ignoreallowance' => 'required|numeric|between:0,1',

        ];
    protected $fillable
        = ['date', 'amount', 'description', 'accountfrom_id', 'accountto_id',
           'user_id','ignoreallowance'];
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $appends = ['beneficiary', 'category', 'budget'];

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

        foreach ($this->components()->get() as $component) {
            if ($component->type == 'budget') {
                return $component;
            }
        }

        return null;
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
     * Get the unencrypted description.
     *
     * @param $value
     *
     * @return string
     */
    public function getDescriptionAttribute($value)
    {
        return Crypt::decrypt($value);
    }

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
     * Encrypt the description.
     *
     * @param $value
     */
    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = Crypt::encrypt($value);
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
