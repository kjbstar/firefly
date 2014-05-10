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
        $user = Auth::user();
        if(is_null($user)) {
            $user = $predictable->user()->first();
        }

        $query = $user->transactions()
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
        foreach ($result as $transaction) {
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

        /** @var $transaction Transaction */
        $transaction = Transaction::find($payload['transaction_id']);
        // already linked?
        if (!is_null($transaction->predictable_id)) {
            return;
        }

        // find matching predictable(s):
        $query = Auth::user()->predictables()
            ->where('inactive', 0)
            ->where('description', $transaction->description);
        $query->distinct();
        $query->leftJoin('component_predictable', 'component_predictable.predictable_id', '=', 'predictables.id');
        $query->where(
            function ($query) use ($transaction) {
                foreach ($transaction->components as $search) {
                    $query->orWhere('component_predictable.component_id', $search->id);
                }
            }
        );


        /** @var $predictable Predictable */
        $predictable = $query->get(['predictables.*'])->first();
        if (!is_null($predictable)) {
            // check amount.
            if ($transaction->amount >= $predictable->maximumAmount()
                && $transaction->amount <= $predictable->minimumAmount()
            ) {
                $transaction->predictable()->associate($predictable);
                $transaction->save();
            }
        }
        $job->delete();
    }

} 