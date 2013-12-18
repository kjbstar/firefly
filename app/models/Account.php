<?php

use Carbon\Carbon as Carbon;

class Account extends Eloquent
{

    public static $rules
        = ['name'               => 'required|between:1,50',
           'openingbalance'     => 'required|numeric',
           'openingbalancedate' => 'required|date|after:1950-01-01',
           'hidden'             => 'required|between:0,1',
           'user_id'            => 'required|exists:users,id',];
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    protected $fillable
        = ['name', 'openingbalance', 'openingbalancedate', 'currentbalance',
           'hidden', 'user_id'];

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
     * Returns the transfers to this account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transfersto()
    {
        return $this->hasMany('Transfer', 'accountto_id');
    }

    /**
     * Returns the transfers from this account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transfersfrom()
    {
        return $this->hasMany('Transfer', 'accountfrom_id');
    }

    /**
     * Returns the balance on a certain date.
     *
     * @param Carbon $date
     *
     * @return float
     */
    public function balanceOnDate(Carbon $date)
    {
        // first two days, remember for 60 minutes
        if ($date->diffInDays($date) < 3) {
            $remember = 60;
        } else {
            $remember = 7 * 24 * 60;
        }

        return floatval(
            $this->balancemodifiers()->where(
                'date', '<=', $date->format('Y-m-d')
            )->remember($remember)->sum('balance')
        );
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
     * Predict what the balance will be on the given date
     *
     * @param \Carbon\Carbon $date
     *
     * @return float
     */
    public function predictOnDate(Carbon $date)
    {
        $data = [];
        $data['most'] = 0;
        $data['least'] = 0;
        $data['prediction'] = 0;
        /*
         * The prediction contains the amount we'll expect to be
         * spent on the given date. It might be 50, so we expect the
         * spent amount to be 50 for that day.
         *
         * The "most" index contains the amount most spent on this day of the
         *  month ever. Predicting for the 4th day of the month,
         * you might have once spent 500 euro's that day. This var will
         * reflect that.
         *
         * The "least" contains the amount at the day you spent the least
         * money.
         */

        // Get all transactions on that day, grouped by
        // the day of the month and the month:
        $list = $this->transactions()->expenses()->where('ignore', 0)
            ->where(
                DB::Raw('DATE_FORMAT(`date`,"%d")'), '=', $date->format('d')
            )->groupBy('day')->get(
                [DB::Raw('DATE_FORMAT(`date`,"%d-%m") as `day`'),
                DB::Raw('SUM(`amount`) as `dayamount`')]
            );
        Log::debug('Days for '.$date->format('d-m-Y').': ' . count($list));
        $sum = 0;
        foreach ($list as $index => $entry) {
            $amount = floatval($entry->dayamount) * -1;
            $sum += $amount;

            // more than the current 'most expensive day ever'?
            if ($amount > $data['most']) {
                $data['most'] = $amount;
            }
            // first entry is 'least' by default (otherwise it would stick at
            // zero)
            if ($index === 0) {
                $data['least'] = $amount;
            }
            if ($amount < $data['least']) {
                $data['least'] = $amount;
            }
        }
        Log::debug('Total amount spent on this day: ' . $sum);
        // the actual prediction:
        $count = count($list);
        $data['prediction'] = $count > 1 ? $sum / count($list) : $sum;

        Log::debug('Prediction voor '.$date->format('d-m-Y').': '.print_r
            ($data,true));
        return $data;
    }

    public function transactions()
    {
        return $this->hasMany('Transaction');
    }

    public function getNameAttribute($value)
    {
        return Crypt::decrypt($value);
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = Crypt::encrypt($value);
    }

    public function getOpeningbalancedateAttribute($value)
    {
        if (is_null($value)) {
            return null;
        }

        return new Carbon($value);
    }

    public function scopeNotHidden($query)
    {
        return $query->where('hidden', 0);
    }

    public function setOpeningbalancedateAttribute($value)
    {
        if ($value instanceof Carbon) {
            $this->attributes['openingbalancedate'] = $value->format('Y-m-d');
        } else {
            if ($value === "") {
                $this->attributes['openingbalancedate'] = null;
            } else {
                $this->attributes['openingbalancedate'] = $value;
            }
        }
    }

}
