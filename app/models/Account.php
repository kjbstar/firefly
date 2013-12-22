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

        // Get all transactions on that day, grouped by
        // the day of the month and the month:
        $predictionStart = Setting::getSetting('predictionStart');

        $list = $this->transactions()->expenses()->
            where('date','>',$predictionStart->value)->
            where(
            'ignore', 0
        )->onDayOfMonth($date)->groupBy('day')->get(
                [DB::Raw('DATE_FORMAT(`date`,"%d-%m") as `day`'),
                DB::Raw('SUM(`amount`) as `dayamount`')]
            );
        Log::debug('Days for ' . $date->format('d-m-Y') . ': ' . count($list));
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

        Log::debug(
            'Prediction voor ' . $date->format('d-m-Y') . ': ' . print_r(
                $data, true
            )
        );

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
