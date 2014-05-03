<?php


/**
 * Class PredictableQueue
 */
class PredictableQueue
{

    /**
     * @param $job
     * @param $payload
     */
    public function scan($job, $payload)
    {
        $predictable = Predictable::find($payload['predictable_id']);
        $this->processPredictable($job, $predictable);
    }

    /**
     * @param                                                 $job
     * @param Predictable                                     $predictable
     */
    public function processPredictable($job, Predictable $predictable)
    {
        // find transactions for this predictable:
        $query = Auth::user()->transactions()
            ->where('amount', '>=', $predictable->maximumAmount())
            ->where('amount', '<=', $predictable->minimumAmount())
            ->where('description', $predictable->description);
        $query->distinct();
        $query->leftJoin('component_transaction', 'component_transaction.transaction_id', '=', 'transactions.id');
        $query->where(
            function ($query) use ($predictable) {
                foreach ($predictable->components as $search) {
                    $query->orWhere('component_transaction.component_id', $search->id);
                }
            }
        );
        $result = $query->get(['transactions.*']);

        // update each transaction found:
        /** @var $transaction Transaction */
        foreach($result as $transaction) {
            $transaction->predictable()->associate($predictable);
            $transaction->save();
        }
        $job->delete();
    }

    /**
     * @param $job
     * @param $payload
     */
    public function processTransaction($job, $payload)
    {
//        $transaction = Transaction::find($payload['transaction_id']);
//
//
//        if (!is_null($transaction->predictable()->first())) {
//            return;
//        }
//
//        $user = Auth::user();
//        if (is_null($user)) {
//            $user = User::find($transaction->user_id);
//        }
//        // will this one fit in any of the predictables?
//        foreach ($user->predictables()->get() as $predictable) {
//            Log::debug('Checking ' . $predictable->description);
//            $lowLimit = $predictable->amount * (1 - ($predictable->pct / 100));
//            $highLimit = $predictable->amount * (1 + ($predictable->pct / 100));
//            $requiredComponents = [];
//            foreach ($predictable->components as $c) {
//                $requiredComponents[] = $c->id;
//            }
//            sort($requiredComponents);
//            Log::debug(
//                'Required components: ' . print_r($requiredComponents, true)
//            );
//
//            $components = [];
//            foreach ($transaction->components()->get() as $c) {
//                $components[] = $c->id;
//            }
//            sort($components);
//            Log::debug('Found components: ' . print_r($components, true));
//            if ($components === $requiredComponents
//                && $transaction->description === $predictable->description
//                && $transaction->amount >= $highLimit
//                && $transaction->amount <= $lowLimit
//            ) {
//                Log::debug('match!');
//                // update transaction
//                $transaction->predictable()->associate($predictable);
//                $transaction->save();
//                break;
//            }
//        }
//        $job->delete();
    }

} 