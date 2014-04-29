<?php

use Carbon\Carbon as Carbon;

require_once(app_path() . '/helpers/AccountHelper.php');

/**
 * Account
 *
 * @property integer $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property integer $user_id
 * @property string $name
 * @property float $openingbalance
 * @property \Carbon\Carbon $openingbalancedate
 * @property float $currentbalance
 * @property boolean $inactive
 * @property boolean $shared
 * @property-read \User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\Transfer[] $transfersto
 * @property-read \Illuminate\Database\Eloquent\Collection|\Predictable[] $predictables
 * @property-read \Illuminate\Database\Eloquent\Collection|\Transfer[] $transfersfrom
 * @property-read \Illuminate\Database\Eloquent\Collection|\Balancemodifier[] $balancemodifiers
 * @property-read \Illuminate\Database\Eloquent\Collection|\Transaction[] $transactions
 * @method static \Account notInactive() 
 * @method static \Account shared() 
 * @method static \Account notShared() 
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
        $now = new Carbon;
        if ($now->diffInMonths($date) > 2) {
            $cacheTime = 20160;
        } else {
            $cacheTime = 10;
        }
        /** @noinspection PhpUndefinedFieldInspection */
        $key = $date->format('Y-m-d') . $this->id . '-balanceOndate';

        if (cache::has($key)) {
            // @codeCoverageIgnoreStart
            return Cache::get($key);
            // @codeCoverageIgnoreEnd
        } else {

            $r = floatval($this->balancemodifiers()->where('date', '<=', $date->format('Y-m-d'))->sum('balance'));
            Cache::put($key, $r, $cacheTime);
            return $r;
        }
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
        $data = [
            'prediction'   => [
                'most'       => 0,
                'least'      => 0,
                'prediction' => 0
            ],
            'transactions' => [],
            'predictables' => []
        ];

        // dates
        $current = clone $date;
        $dateDay = intval($date->format('d'));
        $predictionDate = AccountHelper::getPredictionStart();
        // we set current to the first day of the month:
        $current->firstOfMonth();
        $current->subMonth();

        // between $predictionDate and $date
        // there are X occurences of the day $date
        // ex: between 1-jan-2014 and 16-apr-2014 there
        // is: 16-jan, 16-feb,16-march.
        // we need those dates.
        $days = [];
        while ($current >= $predictionDate) {
            $daysInMonth = intval($current->format('t'));
            $year = intval($current->format('Y'));
            $month = intval($current->format('m'));
            if ($daysInMonth >= $dateDay) {
                $current->setDate($year, $month, $dateDay);
            } else {
                Log::error('We cannot predict for this exact day. Fallback to day #' . $daysInMonth);
                $current->setDate($year, $month, $daysInMonth);
            }
            $days[] = clone $current;
            $current->firstOfMonth();
            $current->subMonth();
        }
        $sum = 0;

        // debug loop for debug debug!
        Log::debug('Predicting on account ' . $this->name . ' for day: ' . $date->format('M jS Y'));
        foreach ($days as $currentDay) {
            Log::debug('Source for prediction: ' . $currentDay->format('M jS Y'));
        }
        // when a day has zero transactions
        // it does not influence the average, and vice versa.
        $influences = 0;

        foreach ($days as $index => $currentDay) {
            // the query for transactions on this day:
            $query = $this->transactions()->expenses()->afterDate($predictionDate)->where('ignoreprediction', 0)
                ->whereNull('predictable_id')->onDay($currentDay);
            $amount = floatval($query->sum('amount')) * -1;
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
            // then re-check the least amount:
            if (($amount != 0 && $amount < $data['prediction']['least']) || $data['prediction']['least'] == 0) {
                $data['prediction']['least'] = $amount;
            }
            if ($amount > 0) {
                $influences++;
            }
            Log::debug('Amount for ' . $currentDay->format('M jS Y') . ': ' . $amount);
        }
        Log::debug(
            'Final calculation for ' . $date->format('M jS Y') . ' [most/least/sum]: ' . $data['prediction']['most']
            . '/' . $data['prediction']['least']
            . '/' . $sum
        );
        // now we have the amount for the current day,
        // and we work on the predictables for this day:

        $predictables = $this->predictables()->active()->where('dom', $dateDay)->get();
        Log::debug('Found ' . count($predictables) . ' predictable expenses for ' . $date->format('M jS Y'));

        // if there are predictables for this day, it does influence the number of days:
        if (count($predictables) > 0) {
            $influences++;
        }

        $predictableSum = 0;
        foreach ($predictables as $p) {
            // predictables that were paid in this month
            // already are ignored.
            $ct = $p->transactions()->inMonth($date)->count();
            if ($ct == 0) {
                // if they ARE in this month, we use the number to
                // finetune the $data['prediction'] array
                $amount = ($p->amount * -1);
                $sum += $amount;
                $predictableSum += $amount;
                // update the least / most if need be:
                Log::debug(
                    'Predictable "' . $p->description . '" is expected to add ' . ($p->amount * -1) . ' to the sum of '
                    . $date->format('M jS Y') . '.'
                );

                $p->date = new Carbon('2012-01-' . $p->dom);
                // and we save it:
                $data['predictables'][] = $p;
            }
        }
        // update most/least sums:
        if ($predictableSum > $data['prediction']['most']) {
            $data['prediction']['most'] = $predictableSum;
            Log::debug(
                'All ' . count($predictables) . ' predictable(s) for this day add up to an expected expense of '
                . $predictableSum . '.'
            );
        }
        if ($predictableSum > 0 && $predictableSum < $data['prediction']['least']) {
            $data['prediction']['least'] = $predictableSum;
        }


        // the actual prediction (three ways of doing it):
        // TODO switch by setting?

        $data['prediction']['prediction'] = array_sum([$data['prediction']['most'] + $data['prediction']['least']]) / 2;
        $data['prediction']['prediction_alt1'] = $influences > 0 ? $sum / $influences : $sum;
        $data['prediction']['prediction_alt2'] = count($days) > 0 ? $sum / count($days) : $sum;


        // in order to spice up the charts, we add two intermediate lines called
        // (how original)

        Log::debug(
            'Final prediction for ' . $date->format('M jS Y') . '[most/least/sum/avg]' .
            $data['prediction']['most'] . '/' .
            $data['prediction']['least'] . '/' .
            $sum . '/' .
            $data['prediction']['prediction']

        );

        return $data;

    }

    /**
     * @param Carbon $date
     *
     * @return mixed
     */
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
