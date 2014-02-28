<?php


class PredictableQueue
{

    public function scan($job, Predictable $predictable) {
        $query = Auth::user()->transactions()->whereNull('predictable_id');
        $this->processPredictable($job,$predictable,$query);
    }
    public function scanAll($job, Predictable $predictable) {
        $query = Auth::user()->transactions();
        $this->processPredictable($job,$predictable,$query);
    }

    public function processPredictable($job, Predictable $predictable,Illuminate\Database\Eloquent\Relations\HasMany $set)
    {
        Log::debug('Looking for ' . $predictable->description);
        // find transactions in the range of this predictable:


        $lowLimit = $predictable->amount*(1-($predictable->pct/100));
        $highLimit = $predictable->amount*(1+($predictable->pct/100));
        $requiredComponents = [];
        foreach ($predictable->components as $c) {
            $requiredComponents[] = $c->id;
        }
        sort($requiredComponents);
        Log::debug('Required components: ' . print_r($requiredComponents,true));

        $transactions = $set
            ->where('amount', '>=', $highLimit)->where(
                'amount', '<=', $lowLimit
            )->get();
        Log::debug('Found ' . $transactions->count() .' possible transactions');

        foreach ($transactions as $t) {
            $components = [];
            foreach ($t->components as $c) {
                $components[] = $c->id;
            }
            sort($components);
            Log::debug('Found components: ' . print_r($components,true));
            if($components == $requiredComponents) {
                Log::debug('Match on components!');
            }

            if ($components == $requiredComponents
                && $t->description === $predictable->description
            ) {
                // update transaction
                $t->predictable()->associate($predictable);
                $t->save();
            }
        }
        $job->delete();
    }

    public function processTransaction($job, Transaction $transaction)
    {
        if (!is_null($transaction->predictable()->first())) {
            return;
        }
        // will this one fit in any of the predictables?
        foreach (Auth::user()->predictables()->get() as $predictable) {
            $lowLimit = $predictable->amount * 0.9;
            $highLimit = $predictable->amount * 1.1;
            $requiredComponents = [];
            foreach ($predictable->components as $c) {
                $requiredComponents[] = $c->id;
            }
            sort($requiredComponents);

            $components = [];
            foreach ($transaction->components as $c) {
                $components[] = $c->id;
            }
            sort($components);
            if ($components === $requiredComponents
                && $transaction->description === $predictable->description
            ) {
                // update transaction
                $transaction->predictable()->associate($predictable);
                $transaction->save();
                break;
            }
        }
        $job->delete();
    }

} 