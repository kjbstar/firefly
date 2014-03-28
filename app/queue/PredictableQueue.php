<?php


class PredictableQueue
{

    public function scan($job, $payload)
    {
        $predictable = Predictable::find($payload['predictable_id']);
        Log::debug('Trigger PredictableQueue[scan]!');
        $user = Auth::user();
        if(is_null($user)) {
            $user = User::find($predictable->user_id);
        }

        $query = $user->transactions()->whereNull('predictable_id');
        $this->processPredictable($job, $predictable, $query);
    }

    public function scanAll($job, $payload)
    {
        $predictable = Predictable::find($payload['predictable_id']);
        Log::debug('Trigger PredictableQueue[scanAll]!');
        $user = Auth::user();
        if(is_null($user)) {
            $user = User::find($predictable->user_id);
        }

        $query = $user->transactions();
        $this->processPredictable($job, $predictable, $query);
    }

    public function processPredictable(
        $job, Predictable $predictable,
        Illuminate\Database\Eloquent\Relations\HasMany $set
    ) {
        Log::debug('Trigger PredictableQueue[processPredictable]!');
        Log::debug('Looking for ' . $predictable->description);
        // find transactions in the range of this predictable:


        $lowLimit = $predictable->amount * (1 - ($predictable->pct / 100));
        $highLimit = $predictable->amount * (1 + ($predictable->pct / 100));
        $requiredComponents = [];
        foreach ($predictable->components as $c) {
            $requiredComponents[] = $c->id;
        }
        sort($requiredComponents);
        Log::debug(
            'Required components: ' . print_r($requiredComponents, true)
        );

        $transactions = $set->where('amount', '>=', $highLimit)->where(
                'amount', '<=', $lowLimit
            )->get();
        Log::debug(
            'Found ' . $transactions->count() . ' possible transactions'
        );

        foreach ($transactions as $t) {
            Log::debug('Now checking '.$t->description.' with amount ' . $t->amount.' on date'. $t->date->format
                    ('d-m-Y'));
            $components = [];
            foreach ($t->components as $c) {
                $components[] = $c->id;
            }
            sort($components);
            Log::debug('Found components: ' . print_r($components, true));
            if ($components == $requiredComponents) {
                Log::debug('Match on components!');
            }

            if ($components == $requiredComponents
                && $t->description === $predictable->description
            ) {
                Log::debug('Match on description! Updating transaction #' . $t->id);
                // update transaction
                $t->predictable()->associate($predictable);
                $result = $t->save();
                Log::debug('Saved transaction: ' . $result.' (#'.$predictable->id.')');
            } else {
                Log::debug('No match on description! NOT updating transaction #' . $t->id);
            }
        }
        $job->delete();
    }

    public function processTransaction($job, $payload)
    {
        $transaction = Transaction::find($payload['transaction_id']);



        if (!is_null($transaction->predictable()->first())) {
            return;
        }

        $user = Auth::user();
        if(is_null($user)) {
            $user = User::find($transaction->user_id);
        }
        // will this one fit in any of the predictables?
        foreach ($user->predictables()->get() as $predictable) {
            Log::debug('Checking ' . $predictable->description);
            $lowLimit = $predictable->amount * (1 - ($predictable->pct / 100));
            $highLimit = $predictable->amount * (1 + ($predictable->pct / 100));
            $requiredComponents = [];
            foreach ($predictable->components as $c) {
                $requiredComponents[] = $c->id;
            }
            sort($requiredComponents);
            Log::debug(
                'Required components: ' . print_r($requiredComponents, true)
            );

            $components = [];
            foreach ($transaction->components()->get() as $c) {
                $components[] = $c->id;
            }
            sort($components);
            Log::debug('Found components: ' . print_r($components, true));
            if ($components === $requiredComponents
                && $transaction->description === $predictable->description
                && $transaction->amount >= $highLimit
                && $transaction->amount <= $lowLimit
            ) {
                Log::debug('match!');
                // update transaction
                $transaction->predictable()->associate($predictable);
                $transaction->save();
                break;
            }
        }
        $job->delete();
    }

} 