<?php


namespace Firefly\Storage\Transaction;


class EloquentTransactionRepository implements TransactionRepositoryInterface
{

    public function predictionBase(\Account $account, \Carbon\Carbon $date)
    {
        $settings = \App::make('Firefly\Storage\Setting\SettingRepositoryInterface');

        $dayOfPrediction = $date->format('d');
        $predictionStartValue = $settings->getSettingValue('predictionStart');
        $predictionStart = is_null($predictionStartValue) ? \Carbon\Carbon::create()
            : new \Carbon\Carbon($predictionStartValue);
        $predictionStartValue = is_null($predictionStartValue) ? $predictionStart->format('Y-m-d')
            : $predictionStartValue;

        return \Auth::user()->transactions()
            ->expenses()
            ->where(\DB::Raw('DATE_FORMAT(`date`,"%d")'), '=', $dayOfPrediction)
            ->where('ignoreprediction', 0)
            ->where('account_id', $account->id)
            ->where('date', '>', $predictionStartValue)
            ->groupBy('day')
            ->get(
                [
                    \DB::Raw('DATE_FORMAT(`date`,"%d-%m-%Y") as `day`'),
                    \DB::Raw('AVG(`amount`) as `average_of_day`'),
                    \DB::Raw('SUM(`amount`) as `sum_of_day`')
                ]
            );
    }
}