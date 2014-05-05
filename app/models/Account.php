<?php

use Carbon\Carbon as Carbon;

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
        $key = $this->id . $date->format('dmy') . 'balanceOnDate';
        if (Cache::has($key)) {
            return Cache::get($key);
        } else {
            $r = floatval($this->balancemodifiers()->where('date', '<=', $date->format('Y-m-d'))->sum('balance'));
            Cache::forever($key, $r);
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
     * @param Carbon $date
     *
     * @return mixed
     */
    public function predictOnDate(Carbon $date)
    {
        $cacheKey = $this->id . '-' . $date->format('dmy') . '-predictOnDate';
        if (Cache::has($cacheKey)) {
            //return Cache::get($cacheKey);
        }
        // prediction setting:
        $predictionStart = Setting::getSetting('predictionStart')->value->format('Y-m-d');

        $dayOfPrediction = $date->format('d');

        $queryText
            = '
        SELECT
          MAX(`sum`) as `min`,
          MIN(`sum`) as `max`,
          AVG(`average`) as `avg`,
          AVG(`sum`) as `sum_avg`
        FROM (
          SELECT
            DATE_FORMAT(`date`,"%d-%m-%Y") as `day`,
            AVG(`amount`) as `average`,
            SUM(`amount`) as `sum`
          FROM `transactions`
          WHERE `amount` < 0
          AND   DATE_FORMAT(`date`,"%d") = "' . $dayOfPrediction . '"
          AND   `ignoreprediction` = 0
          AND   `account_id` = ' . $this->id . '
          AND   `date` > "' . $predictionStart . '"
          GROUP BY `day`
          ORDER BY `date`) as `t`;';

        // number of months between $date and start of prediction.
        $diff = $date->diffInMonths(Setting::getSetting('predictionStart')->value);
        $set = DB::selectOne($queryText);
        $data['most'] = floatval($set->max) * -1;
        $data['least'] = floatval($set->min) * -1;
        Log::debug(
            'Diff in months between ' . $date->format('d-m-Y') . ' and ' . Setting::getSetting(
                'predictionStart'
            )->value->format('d-m-Y') . ': ' . $diff
        );
        $data['prediction']
            = $diff != 0 ? (floatval($set->sum_avg) * -1) / $diff : (floatval($set->sum_avg) * -1);

        /**
         * The  prediction is done by
         */
        Cache::forever($cacheKey, $data);


        return $data;
    }

    /**
     * Return what the prediction for $date is based on.
     *
     * @param Carbon $date
     */
    public function predictionInformation(Carbon $date)
    {
        $predictionStart = Setting::getSetting('predictionStart')->value->format('Y-m-d');
        $transactions = Auth::user()->transactions()
            ->where(DB::Raw('DATE_FORMAT(`date`,"%d")'), '=', $date->format('d'))
            ->where('date', '>', $predictionStart)
            ->where('ignoreprediction', 0)
            ->where('account_id', $this->id)
            ->expenses()
            ->get();

        return $transactions;

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
