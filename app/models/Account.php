<?php

use Carbon\Carbon as Carbon;

/**
 * Class Account
 */
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
     * Returns the transfers coming in to this account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transfersto()
    {
        return $this->hasMany('Transfer', 'accountto_id');
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
        if ($date < $this->openingbalancedate) {
            $date = $this->openingbalancedate;
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
     * money. So either 0 or more.
     *
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

        $predictionStart = Setting::getSetting('predictionStart');
        $predictionDate = new Carbon($predictionStart->value);
        Log::debug('Predicting for ' . $date->format('d-M-Y'));

        // between $predictionDate and $date
        // there are X occurences of the day $date
        // ex: between 1-jan-2014 and 16-apr-2014 there
        // is: 16-jan, 16-feb,16-march.
        // we need those dates.
        $current = clone $date;
        $dateDay = $date->format('d');
        $days = [];
        while ($current >= $predictionDate) {

            // if $current is in the same month as the
            // $date var, we skip it, because it's pretty pointless
            // to compare the current month with itself.
            // this happens on 31-mar, which jumps back to 1-mar.
            $currentDay = $current->format('d');
            Log::debug(
                'currentDay: ' . $currentDay . ' vs dateDay: ' . $dateDay
            );

            if ($current != $date && $dateDay == $currentDay) {
                $days[] = clone $current;
                Log::debug('Added to days[]: ' . $current->format('d-M-Y'));
            }
            $current->subMonth();
        }
        // we need a prediction now, based on these dates:
        $sum = 0;
        foreach ($days as $index => $currentDay) {
            $amount = floatval(
                $this->transactions()->expenses()->where(
                        'date', '>', $predictionStart->value
                    )->where(
                        'ignore', 0
                    )->onDay($currentDay)->sum('amount')
            );
            $amount = $amount * -1;
            Log::debug(
                'Amount for ' . $currentDay->format('d-M-Y') . ': ' . mf(
                    $amount
                )
            );

            // use this amount to do the prediction:
            $sum += $amount;

            // more than the current 'most expensive day ever'?
            if ($amount > $data['most']) {
                $data['most'] = $amount;
            }
            // first entry is 'least' by default (otherwise it would stick at
            // zero)
            if ($index == 0) {
                $data['least'] = $amount;
            }
            if ($amount < $data['least']) {
                $data['least'] = $amount;
            }
        }
        // the actual prediction:
        $count = count($days);
        $data['prediction'] = $count > 1 ? $sum / $count : $sum;

        return $data;


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
     * Decrypt the name on retrieval.
     *
     * @param $value
     *
     * @return string
     */
    public function getNameAttribute($value)
    {
        return Crypt::decrypt($value);
    }

    /**
     * Encrypt the name on storage.
     *
     * @param string $value The unencrypted name
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = Crypt::encrypt($value);
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
     * Shows only not hidden accounts.
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeNotHidden($query)
    {
        return $query->where('hidden', 0);
    }

}
