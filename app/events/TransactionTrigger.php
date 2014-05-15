<?php
use Carbon\Carbon as Carbon;

/**
 * Class TransactionTrigger
 */
class TransactionTrigger
{

    /**
     * Triggers on create a transaction
     *
     * @param Transaction $transaction
     *
     * @return bool
     */
    public function createTransaction(Transaction $transaction)
    {
        Log::debug('Start with createTransactionTrigger for ' . $transaction->description);
        $account = $transaction->account()->first();
        if (is_null($account) || is_null($transaction->account_id)) {
            Session::flash('error_extended', 'Transaction without account is impossible.');
            Log::error('No account, return FALSE');
            return false;
        }
        // update the account:
        if ($transaction->date < $account->openingbalancedate) {
            Session::flash(
                'error_extended', 'The transaction date cannot be before the account\'s opening date ('
                . $account->openingbalancedate->format('Y-m-d') . ').'
            );
            Log::error('Date before account date, return FALSE');
            return false;
        }


        $account->currentbalance += floatval($transaction->amount);
        $account->save();


        // update or create balancemodifier on that date:
        $balanceModifier = $account->balancemodifiers()->onDay($transaction->date)->first();
        if (is_null($balanceModifier)) {
            $balanceModifier = new Balancemodifier;
            $balanceModifier->account()->associate($account);
            $balanceModifier->balance = 0;
            $balanceModifier->date = $transaction->date;
        }
        $balanceModifier->balance += floatval($transaction->amount);
        $balanceModifier->save();
        Cache::userFlush();

        return true;
    }

    /**
     * Triggers on the deletion of a transaction.
     *
     * @param Transaction $transaction
     *
     * @return bool
     */
    public function deleteTransaction(Transaction $transaction)
    {
        $account = $transaction->account()->first();
        $account->currentbalance -= floatval($transaction->amount);
        $account->save();


        // update or create balancemodifier
        $balanceModifier = $account->balancemodifiers()->onDay($transaction->date)->first();
        if (is_null($balanceModifier)) {
            $balanceModifier = new Balancemodifier;
            $balanceModifier->account()->associate($account);
            $balanceModifier->balance = 0;
            $balanceModifier->date = $transaction->date;
        }
        $balanceModifier->balance -= floatval($transaction->amount);
        $balanceModifier->save();
        Cache::userFlush();

        return true;
    }

    /**
     *
     *
     * When a transaction is edited the first thing
     * checked is the new date of the transaction. This influences
     * two balance modifiers and cannot be done when the
     * transaction's new date is before the opening date of the (new) account
     * . This is only relevant when that date has changed. This step updates
     * nothing, it just checks.
     *
     * Updates are in this order:
     *
     * AccountID changed: update the balance on the old account,
     * and then update it on the new account. Use the old date
     * for the balance modifiers.
     *
     * Date changed: update the old balance modifier (create if exists) and
     * put the amount "back". Then, update the new balance modifier and do
     * the same.
     *
     * Amount changed: update the balance modifier.
     *
     * @param Transaction $transaction
     *
     * @return bool
     */
    public function editTransaction(Transaction $transaction)
    {
        Log::debug('Trigger edit on transaction #' . $transaction->id);
        $account = $transaction->account()->first();
        if ($transaction->date < $account->openingbalancedate) {
            Session::flash(
                'error_extended', 'The transaction\'s data cannot be before the account\'s opening balance date.'
            );
            Log::debug('Date before date');
            return false;
        }
        $triggerPrediction = false;

        if ($account->id != intval($transaction->getOriginal('account_id'))) {
            $triggerPrediction = true;
            $this->triggerAccountChanged($transaction);
        }
        if ($transaction->date->format('Y-m-d') != $transaction->getOriginal('date')) {
            $triggerPrediction = true;
            $this->triggerDateChanged($transaction);
        }
        if ($transaction->amount != floatval($transaction->getOriginal('amount'))) {
            $triggerPrediction = true;
            $this->triggerAmountChanged($transaction);
        }
        // loop all predictables:
        if ($triggerPrediction) {
            Log::debug('Triggering predictions!');
            Queue::push('PredictableQueue@processTransaction', ['transaction_id' => $transaction->id]);
        } else {
            Log::debug('No relevant changes, not triggering prediction.');
        }
        Cache::userFlush();

        return true;
    }

    /**
     * AccountID changed: update the balance on the old account,
     * and then update it on the new account. Use the old date
     * for the balance modifiers.
     *
     * @param Transaction $transaction The transaction.
     */
    private function triggerAccountChanged(Transaction $transaction)
    {
        $date = new Carbon($transaction->getOriginal('date'));
        $newAccount = Auth::user()->accounts()->find($transaction->account_id);
        $oldAccount = Auth::user()->accounts()->find($transaction->getOriginal('account_id'));

        // remove the amount from the old BM
        $oldBm = $oldAccount->balancemodifiers()->onDay($date)->first();
        if (is_null($oldBm)) {
            $oldBm = new Balancemodifier();
            $oldBm->account()->associate($oldAccount);
            $oldBm->date = $date;
            $oldBm->balance = 0;
        }
        $oldBm->balance -= floatval($transaction->getOriginal('amount'));
        $oldBm->save();
        // update the account's current balance:
        $oldAccount->currentbalance -= floatval($transaction->getOriginal('amount'));
        $oldAccount->save();

        // add the amount to the new BM:
        $newBm = $newAccount->balancemodifiers()->onDay($date)->first();
        if (is_null($newBm)) {
            $newBm = new Balancemodifier();
            $newBm->account()->associate($newAccount);
            $newBm->date = $date;
            $newBm->balance = 0;
        }
        $newBm->balance += floatval($transaction->getOriginal('amount'));
        $newBm->save();

        // update the new account:
        $newAccount->currentbalance += floatval($transaction->getOriginal('amount'));
        $newAccount->save();

        // return, we're done here!

    }

    /**
     * Date changed: update the old balance modifier (create if exists) and
     * put the amount "back". Then, update the new balance modifier and do
     * the same.
     *
     * @param Transaction $transaction The transaction.
     */
    private function triggerDateChanged(Transaction $transaction)
    {
        $oldDate = new Carbon($transaction->getOriginal('date'));
        $account = $transaction->account()->first();

        $oldBm = $account->balancemodifiers()->onDay($oldDate)->first();
        if (is_null($oldBm)) {
            $oldBm = new Balancemodifier();
            $oldBm->account()->associate($account);
            $oldBm->date = $oldDate;
            $oldBm->balance = 0;
        }
        $oldBm->balance -= floatval($transaction->getOriginal('amount'));
        $oldBm->save();

        // update for new date:
        $newBm = $account->balancemodifiers()->onDay($transaction->date)->first();
        if (is_null($newBm)) {
            $newBm = new Balancemodifier();
            $newBm->account()->associate($account);
            $newBm->date = $transaction->date;
            $newBm->balance = 0;
        }
        $newBm->balance += floatval($transaction->getOriginal('amount'));
        $newBm->save();
    }

    /**
     * The amount has changed.
     *
     * @param Transaction $transaction The transaction
     */
    private function triggerAmountChanged(Transaction $transaction)
    {
        $oldDate = new Carbon($transaction->getOriginal('date'));
        $account = $transaction->account()->first();
        $diff = $transaction->amount - floatval(
                $transaction->getOriginal('amount')
            );
        $balanceModifier = $account->balancemodifiers()->onDay($oldDate)->first();
        if (is_null($balanceModifier)) {
            $balanceModifier = new Balancemodifier();
            $balanceModifier->account()->associate($account);
            $balanceModifier->date = $oldDate;
            $balanceModifier->balance = 0;
        }
        $balanceModifier->balance += $diff;
        $balanceModifier->save();

        $account->currentbalance += $diff;
        $account->save();
    }

    /**
     * @param Transaction $transaction
     */
    public function pushTransaction(Transaction $transaction)
    {
        Queue::push('PredictableQueue@processTransaction', ['transaction_id' => $transaction->id]);
    }

    /**
     * Create the triggers.
     *
     * @param \Illuminate\Events\Dispatcher $events
     *
     */
    public function subscribe(\Illuminate\Events\Dispatcher $events)
    {
        $events->listen('eloquent.creating: Transaction', 'TransactionTrigger@createTransaction');
        // TODO this is too early; the components have not yet been saved.
        //$events->listen('eloquent.created: Transaction', 'TransactionTrigger@pushTransaction');
        // TODO this is too early; the components have not yet been saved.
        $events->listen('eloquent.updated: Transaction', 'TransactionTrigger@pushTransaction');

        $events->listen('eloquent.deleted: Transaction', 'TransactionTrigger@deleteTransaction');
        $events->listen('eloquent.updating: Transaction', 'TransactionTrigger@editTransaction');


    }

}
