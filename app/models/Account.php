<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Account
 *
 * @property integer                                                          $id
 * @property \Carbon\Carbon                                                   $created_at
 * @property \Carbon\Carbon                                                   $updated_at
 * @property integer                                                          $user_id
 * @property string                                                           $name
 * @property float                                                            $openingbalance
 * @property \Carbon\Carbon                                                   $openingbalancedate
 * @property float                                                            $currentbalance
 * @property boolean                                                          $inactive
 * @property boolean                                                          $shared
 * @property-read \User                                                       $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\Transfer[]        $transfersto
 * @property-read \Illuminate\Database\Eloquent\Collection|\Predictable[]     $predictables
 * @property-read \Illuminate\Database\Eloquent\Collection|\Transfer[]        $transfersfrom
 * @property-read \Illuminate\Database\Eloquent\Collection|\Balancemodifier[] $balancemodifiers
 * @property-read \Illuminate\Database\Eloquent\Collection|\Transaction[]     $transactions
 * @method static \Account notInactive()
 * @method static \Account shared()
 * @method static \Account notShared()
 * @property string                                                           $lastactivityedate
 */
class Account extends Eloquent
{

    public static $rules
        = ['name'               => 'required|between:1,40',
           'openingbalance'     => 'required|numeric',
           'openingbalancedate' => 'required|date|after:1950-01-01',
           'inactive'           => 'required|between:0,1',
           'shared'             => 'required|between:0,1',
           'user_id'            => 'required|exists:users,id',];
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $fillable
        = ['name', 'openingbalance', 'openingbalancedate', 'currentbalance',
           'inactive', 'user_id', 'shared'];


    /**
     * Account belongs to a User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('User');
    }

    /**
     * Returns the transfers coming in to this account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transfersto()
    {
        return $this->hasMany('Transfer', 'accountto_id');
    }

    public function predictables()
    {
        return $this->hasMany('Predictable');
    }

    /**
     * Returns the transfers going away from this account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transfersfrom()
    {
        return $this->hasMany('Transfer', 'accountfrom_id');
    }

    /**
     * Get the account's balance modifiers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function balancemodifiers()
    {
        return $this->hasMany('Balancemodifier');
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
     * These values must be converted to a Carbon object.
     *
     * @return array
     */
    public function getDates()
    {
        return ['created_at', 'updated_at', 'openingbalancedate'];
    }

    /**
     * Shows only not inactive accounts.
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeNotInactive($query)
    {
        return $query->where('inactive', 0);
    }

    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeShared($query)
    {
        return $query->where('shared', 1);
    }

    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeNotShared($query)
    {
        return $query->where('shared', 0);
    }

}
