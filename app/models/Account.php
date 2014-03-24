<?php

use Carbon\Carbon as Carbon;

/** @noinspection PhpIncludeInspection */
require_once(app_path() . '/helpers/AccountHelper.php');

/**
 * Class Account
 *
 * @property integer                                                          $id
 * @property integer                                                          $user_id
 * @property \Carbon\Carbon                                                   $created_at
 * @property \Carbon\Carbon                                                   $updated_at
 * @property string                                                           $name
 * @property float                                                            $openingbalance
 * @property Carbon                                                           $openingbalancedate
 * @property float                                                            $currentbalance
 * @property boolean                                                          $hidden
 * @property-read \User                                                       $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\Transfer[]        $transfersto
 * @property-read \Illuminate\Database\Eloquent\Collection|\Transfer[]        $transfersfrom
 * @property-read \Illuminate\Database\Eloquent\Collection|\Balancemodifier[] $balancemodifiers
 * @property-read \Illuminate\Database\Eloquent\Collection|\Transaction[]     $transactions
 * @method static Account notHidden()
 * @property boolean $shared
 * @method static \Illuminate\Database\Query\Builder|\Account whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Account whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Account whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Account whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\Account whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Account whereOpeningbalance($value)
 * @method static \Illuminate\Database\Query\Builder|\Account whereOpeningbalancedate($value)
 * @method static \Illuminate\Database\Query\Builder|\Account whereCurrentbalance($value)
 * @method static \Illuminate\Database\Query\Builder|\Account whereHidden($value)
 * @method static \Illuminate\Database\Query\Builder|\Account whereShared($value)
 * @method static \Account shared()
 */
class Account extends Eloquent
{

    public static $rules
        = ['name'               => 'required|between:1,500',
           'openingbalance'     => 'required|numeric',
           'openingbalancedate' => 'required|date|after:1950-01-01',
           'hidden'             => 'required|between:0,1',
           'shared'             => 'required|between:0,1',
           'user_id'            => 'required|exists:users,id',];
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $fillable
        = ['name', 'openingbalance', 'openingbalancedate', 'currentbalance',
           'hidden', 'user_id','shared'];

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
        if ($date < $this->openingbalancedate) {
            $date = $this->openingbalancedate;
        }

        return floatval(
            $this->balancemodifiers()->where(
                'date', '<=', $date->format('Y-m-d')
            )->sum('balance')
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
    public function predictOnDateExpanded(Carbon $date)
    {
        // data that will be returned:
        $data = ['prediction' => ['most'       => 0, 'least' => 0,
                                  'prediction' => 0,], 'transactions' => [],
                 'predictables' => []];
        // dates
        $current = clone $date;
        $dateDay = intval($date->format('d'));
        $predictionDate = AccountHelper::getPredictionStart();
        Log::debug('Predicting for ' . $this->name . ' on ' . $date->format('d-M-Y'));

        // between $predictionDate and $date
        // there are X occurences of the day $date
        // ex: between 1-jan-2014 and 16-apr-2014 there
        // is: 16-jan, 16-feb,16-march.
        // we need those dates.
        $days = [];
        Log::debug('Start looping over days for ' . $date->format('d-M-Y') . '.');
        while ($current >= $predictionDate) {

            // if $current is in the same month as the
            // $date var, we skip it, because it's pretty pointless
            // to compare the current month with itself.
            // this happens on 31-mar, which jumps back to 1-mar.
            $currentDay = $current->format('d');


            if ($current != $date && $dateDay == $currentDay) {
                Log::debug('Added ' . $current->format('d-M-Y'));
                $days[] = clone $current;
            }
            // submonth jumps the wrong way
            $current->subMonth();
        }
        Log::debug('End of loop');
        // loop over these days
        // (12-jan, 12-feb, 12-mar, etc.)
        $sum = 0;
        Log::debug('Now looping these days.');
        Log::debug('Prediction date: ' . $predictionDate->format('d-m-Y'));
        foreach ($days as $index => $currentDay) {
            // the query for this day:
            $query = $this->transactions()->expenses()->afterDate(
                $predictionDate
            )->where('ignoreprediction', 0)->whereNull('predictable_id')->onDay($currentDay);
            $amount = floatval($query->sum('amount')) * -1;
            Log::debug('Sum for ' . $currentDay->format('d-m-Y') . ': ' . $amount);
            // save the list
            $data['transactions'][$currentDay->format('d-m-Y')] = $query->get();

            // the total amount defines the average later on:
            $sum += $amount;

            // more than the current 'most expensive day ever'?
            if ($amount > $data['prediction']['most']) {
                $data['prediction']['most'] = $amount;
            }
            // first entry is 'least' by default (otherwise it would stick at
            // zero)
            if ($index == 0) {
                $data['prediction']['least'] = $amount;
            }
            if (($amount != 0 && $amount < $data['prediction']['least'])
                || $data['prediction']['least'] == 0
            ) {
                $data['prediction']['least'] = $amount;
            }
            Log::debug(
                $currentDay->format('d-M-Y') . ': Most/least/sum: '
                . $data['prediction']['most'] . '/'
                . $data['prediction']['least'] . '/' . $sum . ' [amount: '
                . $amount . ']'
            );
        }
        // now we have the amount for the current day,
        // and we work on the predictables for this day:

        $predictables = Auth::user()->predictables()->active()->where(
            'dom', $dateDay
        )->get();
        Log::debug('Found ' . count($predictables) . ' predictables on ' . $dateDay . '.');
        $predictableSum = 0;
        foreach ($predictables as $p) {
            // predictables that were paid in this month
            // already are ignored.
            Log::debug('Now at "' . $p->description . '"');
            $count = $p->transactions()->inMonth($date)->count();
            Log::debug('Found ' . $count . ' transactions for this predictable.');
            if ($count == 0) {
                Log::debug($p->description . ' has not been paid yet this month');
                // if they ARE in this month, we use the number to
                // finetune the $data['prediction'] array
                $amount = ($p->amount * -1);
                $sum += $amount;
                $predictableSum += $amount;
                // update the least / most if need be:

                $p->date = new Carbon('2012-01-' . $p->dom);
                // and we save it:
                $data['predictables'][] = $p;
            }
        }
        // update most/least sums:
        if ($predictableSum > $data['prediction']['most']) {
            Log::debug($predictableSum . ' > ' . $data['prediction']['most'] . ', so "most" is updated.');
            $data['prediction']['most'] = $predictableSum;
        } else {
            if ($predictableSum > 0 && $predictableSum < $data['prediction']['least']) {
                Log::debug($predictableSum . ' < ' . $data['prediction']['least'] . ', so "least" is updated.');
                $data['prediction']['least'] = $predictableSum;
            } else {
                Log::debug(
                    $predictableSum . ' is in between ' . $data['prediction']['most'] . ' and  '
                    . $data['prediction']['least'] . ', so nothing is updated.'
                );
            }
        }


        Log::debug('Done looping all days.');
        Log::debug(
            'Most/least/sum: ' . $data['prediction']['most'] . '/'
            . $data['prediction']['least'] . '/' . $sum
        );
        // the actual prediction:
        $count = count($days);
        $data['prediction']['prediction'] = $count > 1 ? $sum / $count : $sum;

        return $data;

    }

    public function predictOnDate(Carbon $date)
    {
        $data = $this->predictOnDateExpanded($date);

        return $data['prediction'];
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
        if (is_null($value)) {
            return null;
        }

        return Crypt::decrypt($value);
    }

    /**
     * Encrypt the name on storage.
     *
     * @param string $value The unencrypted name
     */
    public function setNameAttribute($value)
    {
        if (strlen($value) > 0) {
            $this->attributes['name'] = Crypt::encrypt($value);
        } else {
            $this->attributes['name'] = null;
        }
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

    public function scopeShared($query)
    {
        return $query->where('shared', 1);
    }

}
