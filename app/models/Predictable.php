<?php

/**
 * An Eloquent Model: 'Predictable'
 *
 * @property integer                                                    $id
 * @property \Carbon\Carbon                                             $created_at
 * @property-read \User                                                 $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\Component[] $components
 */
class Predictable extends Eloquent
{
    public static $rules
        = ['description' => 'required|between:1,400',
           'user_id'     => 'required|exists:users,id',
           'dom'         => 'required|numeric|between:1,31'];
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    protected $fillable = ['description', 'user_id', 'dom'];

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
     * Gets the description as a decrypted string.
     *
     * @param $value
     *
     * @return null|string
     */
    public function getDescriptionAttribute($value)
    {
        return is_null($value) ? null : Crypt::decrypt($value);
    }

    /**
     * Set the description as an encrypted string.
     *
     * @param $value
     */
    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = Crypt::encrypt($value);
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