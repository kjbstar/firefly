<?php

/**
 * An Eloquent Model: 'Piggybank'
 *
 * @property integer        $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property integer        $user_id
 * @property string         $name
 * @property float          $amount
 * @property float          $target
 * @property-read \User     $user
 * @method static \Illuminate\Database\Query\Builder|\Piggybank whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Piggybank whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Piggybank whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Piggybank whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\Piggybank whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Piggybank whereAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\Piggybank whereTarget($value)
 */
class Piggybank extends Eloquent
{

    public static $rules
        = ['name'   => 'required|between:1,50', 'amount' => 'required|numeric',
           'target' => 'numeric', 'user_id' => 'required|exists:users,id',
           'order'  => 'numeric|min:0'
        ];
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $fillable
        = ['name', 'amount', 'target', 'user_id', 'order'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('User');
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function getNameAttribute($value)
    {
        return Crypt::decrypt($value);
    }

    /**
     * @param $value
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = Crypt::encrypt($value);
    }

    /**
     * @return float|int
     */
    public function pctFilled()
    {
        if (is_null($this->target) || $this->target == 0) {
            return 0;
        } else {
            $pct = round(($this->amount / $this->target) * 100);

            return $pct >= 100 ? 100 : $pct;
        }
    }
}