<?php

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
        // update the account:
        $account = $transaction->account()->first();
        $account->currentbalance += floatval($transaction->amount);
        $account->save();


        // update or create balancemodifier on that date:
        $balanceModifier = $account->balancemodifiers()->onDay(
            $transaction->date
        )->first();
        if (is_null($balanceModifier)) {
            $balanceModifier = new Balancemodifier;
            $balanceModifier->account()->associate($account);
            $balanceModifier->balance = 0;
            $balanceModifier->date = $transaction->date;
        }
        $balanceModifier->balance += floatval($transaction->amount);
        $balanceModifier->save();
        Cache::flush();

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
        $account->currentbalance -= floatval($transaction->amount) * -1;
        $account->save();


        // update or create balancemodifier
        $balanceModifier = $account->balancemodifiers()->where(
            'date', $transaction->date
        )->first();
        if (is_null($balanceModifier)) {
            $balanceModifier = new Balancemodifier;
            $balanceModifier->account()->associate($account);
            $balanceModifier->balance = 0;
            $balanceModifier->date = $transaction->date;
        }
        $balanceModifier->balance -= floatval($transaction->amount) * -1;
        $balanceModifier->save();

        Cache::flush();

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
     * and then update it on the new account.
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
        Cache::flush();
        $diff = floatval($transaction->amount) - floatval(
                $transaction->getOriginal('amount')
            );

//        $result = $this->

        die('no impl');
        exit;


        if ($diff != 0) {
            $this->triggerTransactionAmountChanged();
        }


        if ($transaction->account_id == $transaction->getOriginal(
                'account_id'
            )
        ) {
            $account = $transaction->account()->first();
            $account->currentbalance += $diff;
            $account->save();

            // switch account for transaction!
            // update or create balancemodifier
            $balanceModifier = $account->balancemodifiers()->where(
                'date', $transaction->date
            )->first();
            if (is_null($balanceModifier)) {
                $balanceModifier = new Balancemodifier;
                $balanceModifier->account()->associate($account);
                $balanceModifier->balance = 0;
                $balanceModifier->date = $transaction->date;
            }
            $balanceModifier->balance += $diff;
            $balanceModifier->save();

            return true;
        } else {
            $oldAccount = Auth::user()->accounts()->find(
                $transaction->getOriginal('account_id')
            );
            $newAccount = Auth::user()->accounts()->find(
                $transaction->account_id
            );
            if ($oldAccount && $newAccount) {
                // update old balance modifier
                $oldBM = $oldAccount->balancemodifiers()->where(
                    'date', $transaction->getOriginal('date')
                )->first();
                if (is_null($oldBM)) {
                    $oldBM = new Balancemodifier;
                    $oldBM->account()->associate($oldAccount);
                    $oldBM->balance = 0;
                    $oldBM->date = $transaction->getOriginal('date');
                }
                $oldBM->balance += $diff;
                $oldBM->save();
                // update new balance modifier
                $newBM = $newAccount->balancemodifiers()->where(
                    'date', $transaction->date
                )->first();
                if (is_null($newBM)) {
                    $newBM = new Balancemodifier;
                    $newBM->account()->associate($newAccount);
                    $newBM->balance = 0;
                    $newBM->date = $transaction->date;
                }
                $newBM->balance += $diff;
                $newBM->save();

                return true;
            }

            return false;
        }
    }

    /**
     * Create the triggers.
     *
     * @param \Illuminate\Events\Dispatcher $events
     *
     */
    public function subscribe(\Illuminate\Events\Dispatcher $events)
    {
        $events->listen(
            'eloquent.creating: Transaction',
            'TransactionTrigger@createTransaction'
        );
        $events->listen(
            'eloquent.deleted: Transaction',
            'TransactionTrigger@deleteTransaction'
        );
        $events->listen(
            'eloquent.updating: Transaction',
            'TransactionTrigger@editTransaction'
        );
    }

}

$subscriber = new TransactionTrigger;

Event::subscribe($subscriber);
