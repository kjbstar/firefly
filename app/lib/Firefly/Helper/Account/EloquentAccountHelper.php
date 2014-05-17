<?php

namespace Firefly\Helper\Account;

class EloquentAccountHelper implements AccountHelperInterface
{

    public function __construct(
        \Firefly\Storage\Setting\SettingRepositoryInterface $settings,
        \Firefly\Storage\Transaction\TransactionRepositoryInterface $transactions
    ) {
        $this->settings = $settings;
        $this->transactions = $transactions;

    }

    public function balanceOnDate(\Account $account, \Carbon\Carbon $date = null)
    {
        $date = is_null($date) ? new \Carbon\Carbon : $date;

        if ($date < $account->openingbalancedate) {
            $date = $account->openingbalancedate;
        }
        $key = $account->id . $date->format('dmy') . 'balanceOnDate';
        if (\Cache::has($key)) {
            return \Cache::get($key);
        } else {
            $r = floatval($account->balancemodifiers()->where('date', '<=', $date->format('Y-m-d'))->sum('balance'));
            \Cache::forever($key, $r);
            return $r;
        }

    }

    public function predictOnDate(\Account $account, \Carbon\Carbon $date)
    {
        $cacheKey = $account->id . '-' . $date->format('dmy') . '-predictOnDate';
        if (\Cache::has($cacheKey)) {
            return \Cache::get($cacheKey);
        }
        // prediction setting:
        $predictionStartValue = $this->settings->getSettingValue('predictionStart');
        $predictionStart = is_null($predictionStartValue) ? \Carbon\Carbon::create()
            : new \Carbon\Carbon($predictionStartValue);
        $predictionStartValue = is_null($predictionStartValue) ? $predictionStart->format('Y-m-d')
            : $predictionStartValue;


        $dayOfPrediction = $date->format('d');

        $queryText
            = '
        SELECT
          MAX(`sum_of_day`) as `min`,
          MIN(`sum_of_day`) as `max`,
          AVG(`average_of_day`) as `avg`,
          AVG(`sum_of_day`) as `sum_avg`
        FROM (
          SELECT
            DATE_FORMAT(`date`,"%d-%m-%Y") as `day`,
            AVG(`amount`) as `average_of_day`,
            SUM(`amount`) as `sum_of_day`
          FROM `transactions`
          WHERE `amount` < 0
          AND   DATE_FORMAT(`date`,"%d") = "' . $dayOfPrediction . '"
          AND   `ignoreprediction` = 0
          AND   `account_id` = ' . $account->id . '
          AND   `date` > "' . $predictionStartValue . '"
          GROUP BY `day`
          ORDER BY `date`) as `t`;';

        // number of months between $date and start of prediction.
        $diff = $date->diffInMonths($predictionStart);
        $set = \DB::selectOne($queryText);
        $data['most'] = floatval($set->max) * -1;
        $data['least'] = floatval($set->min) * -1;
        $data['prediction'] = $diff != 0 ? (floatval($set->sum_avg) * -1) / $diff : (floatval($set->sum_avg) * -1);

        /*
         * If the optimistic (least) prediction is larger than the normal prediction,
         * (ie predicting a bigger expense)
         * we change the optimistic prediction:
         */
        if ($data['least'] > $data['prediction']) {
            $data['least'] = $data['prediction'];
        }

        \Cache::forever($cacheKey, $data);


        return $data;

    }

    public function predictionInformation(\Account $account, \Carbon\Carbon $date)
    {
        $information = $this->transactions->predictionBase($account,$date);

        $information->each(
            function ($x) {
                $x->day = new \Carbon\Carbon($x->day);
            }
        );
        return $information;
    }
} 