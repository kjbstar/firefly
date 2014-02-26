<?php


class PredictableQueue
{

    public function processPredictable($job, Predictable $predictable)
    {
        // find transactions in the range of this predictable:
        $lowLimit = $predictable->amount * 0.9;
        $highLimit = $predictable->amount * 1.1;
        $requiredComponents = [];
        foreach($predictable->components as $c) {
            $requiredComponents[] = $c->id;
        }
        sort($requiredComponents);

        $transactions = Transaction::where('user_id', $predictable->user_id)
            ->where('amount', '>=', $highLimit)->where(
                'amount', '<=', $lowLimit
            )->get();

        foreach ($transactions as $t) {
            $components = [];
            foreach($t->components as $c) {
                $components[] = $c->id;
            }
            sort($components);
            if($components === $requiredComponents && $t->description === $predictable->description) {
                // update transaction
                $t->predictable()->associate($predictable);
                $t->save();
            }
        }
        $job->delete();
    }

    public function processTransaction($job, Transaction $transaction) {
        if(!is_null($transaction->predictable()->first())) {
            return;
        }
        // will this one fit in any of the predictables?
        foreach(Auth::user()->predictables()->get() as $predictable) {
            $lowLimit = $predictable->amount * 0.9;
            $highLimit = $predictable->amount * 1.1;
            $requiredComponents = [];
            foreach($predictable->components as $c) {
                $requiredComponents[] = $c->id;
            }
            sort($requiredComponents);

            $components = [];
            foreach($transaction->components as $c) {
                $components[] = $c->id;
            }
            sort($components);
            if($components === $requiredComponents && $transaction->description === $predictable->description) {
                // update transaction
                $transaction->predictable()->associate($predictable);
                $transaction->save();
                break;
            }
        }
        $job->delete();
    }

} 