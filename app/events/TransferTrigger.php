<?php

/**
 * Class TransferTrigger
 */
class TransferTrigger
{

    /**
     * Reponds to the creation or deletion of a transfer.
     *
     * @param Transfer $transfer
     *
     * @return bool|null
     */
    public function createTransfer(Transfer $transfer)
    {
        Cache::flush();
        // validate the transaction:
        $validator = Validator::make($transfer->toArray(), Transfer::$rules);
        if ($validator->fails()) {
            return false;
        }

        // take the amount from the from account
        $accountFrom = $transfer->accountfrom()->first();
        $accountTo = $transfer->accountto()->first();

        $accountFrom->currentbalance -= floatval($transfer->amount);
        $accountTo->currentbalance += floatval($transfer->amount);
        $accountFrom->save();
        $accountTo->save();

        // update or create balancemodifier for TO account.
        $balanceModifier = $accountTo->balancemodifiers()->where(
            'date', $transfer->date
        )->first();
        if (is_null($balanceModifier)) {
            $balanceModifier = new Balancemodifier;
            $balanceModifier->account()->associate($accountTo);
            $balanceModifier->balance = 0;
            $balanceModifier->date = $transfer->date;
        }
        // add it:
        $balanceModifier->balance += $transfer->amount;
        $balanceModifier->save();
        unset($balanceModifier);

        // update or create balancemodifier for FROM account:
        $balanceModifierFrom = $accountFrom->balancemodifiers()->where(
            'date', $transfer->date
        )->first();
        if (is_null($balanceModifierFrom)) {
            $balanceModifierFrom = new Balancemodifier;
            $balanceModifierFrom->account()->associate($accountFrom);
            $balanceModifierFrom->balance = 0;
            $balanceModifierFrom->date = $transfer->date;
        }
        // add it:
        $balanceModifierFrom->balance -= $transfer->amount;
        $balanceModifierFrom->save();


        return true;
        // add it to the to account
    }

    /**
     * Triggers on the editing of a transfer.
     *
     * @param Transfer $transfer
     *
     * @return bool
     */
    public function editTransfer(Transfer $transfer)
    {
        Cache::flush();
        // no changes in account ID's:
        if ($transfer->getOriginal('accountfrom_id')
            == $transfer->accountfrom_id
            && $transfer->getOriginal('accountto_id') == $transfer->accountto_id
        ) {
            // slechts amount aangepast?
            if (floatval($transfer->getOriginal('amount')) != floatval(
                    $transfer->amount
                )
            ) {
                $diff = floatval($transfer->amount) - floatval(
                        $transfer->getOriginal('amount')
                    );
                $fromAccount = $transfer->accountfrom()->first();
                $toAccount = $transfer->accountto()->first();
                // add to to_account
                // sub from from_account
                $toAccount->currentbalance += $diff;
                $fromAccount->currentbalance -= $diff;
                $toAccount->save();
                $fromAccount->save();

                // update or create balancemodifier for TO account.
                $balanceModifier = $toAccount->balancemodifiers()->where(
                    'date', $transfer->date
                )->first();
                if (is_null($balanceModifier)) {
                    $balanceModifier = new Balancemodifier;
                    $balanceModifier->account()->associate($toAccount);
                    $balanceModifier->balance = 0;
                    $balanceModifier->date = $transfer->date;
                }
                // add it:
                $balanceModifier->balance += $diff;
                $balanceModifier->save();
                unset($balanceModifier);

                // update or create balancemodifier for FROM account:
                $balanceModifierFrom = $fromAccount->balancemodifiers()->where(
                    'date', $transfer->date
                )->first();
                if (is_null($balanceModifierFrom)) {
                    $balanceModifierFrom = new Balancemodifier;
                    $balanceModifierFrom->account()->associate($fromAccount);
                    $balanceModifierFrom->balance = 0;
                    $balanceModifierFrom->date = $transfer->date;
                }
                // add it:
                $balanceModifierFrom->balance -= $diff;
                $balanceModifierFrom->save();


                return true;
            }
        } else {

            // if the from account has changed:
            if ($transfer->getOriginal('accountfrom_id')
                != $transfer->accountfrom_id
            ) {
                // we put the OLD amount back in the from account and sub it from the new
                // from account. Also the NEW amount (if applicable, we dont care).
                $oldFrom = Auth::user()->accounts()->find(
                    $transfer->getOriginal('accountfrom_id')
                );
                $newFrom = Auth::user()->accounts()->find(
                    $transfer->accountfrom_id
                );
                $oldFrom->currentbalance += floatval(
                    $transfer->getOriginal('amount')
                );
                $newFrom->currentbalance -= floatval($transfer->amount);
                $oldFrom->save();
                $newFrom->save();

                // update or create balancemodifier for OLD FROM account.
                $balanceModifier = $oldFrom->balancemodifiers()->where(
                    'date', $transfer->date
                )->first();
                if (is_null($balanceModifier)) {
                    $balanceModifier = new Balancemodifier;
                    $balanceModifier->account()->associate($oldFrom);
                    $balanceModifier->balance = 0;
                    $balanceModifier->date = $transfer->date;
                }
                // add it:
                $balanceModifier->balance += floatval(
                    $transfer->getOriginal('amount')
                );;
                $balanceModifier->save();
                unset($balanceModifier);

                // update or create balancemodifier for NEW FROM account:
                $balanceModifierFrom = $newFrom->balancemodifiers()->where(
                    'date', $transfer->date
                )->first();
                if (is_null($balanceModifierFrom)) {
                    $balanceModifierFrom = new Balancemodifier;
                    $balanceModifierFrom->account()->associate($oldTo);
                    $balanceModifierFrom->balance = 0;
                    $balanceModifierFrom->date = $transfer->date;
                }
                // add it:
                $balanceModifierFrom->balance -= floatval($transfer->amount);
                $balanceModifierFrom->save();
            }
            // if the TO account has changed:
            if ($transfer->getOriginal('accountto_id')
                != $transfer->accountto_id
            ) {
                // we take the old amount back from the old TO account
                // and give the new amount to the new to account

                $oldTo = Auth::user()->accounts()->find(
                    $transfer->getOriginal('accountto_id')
                );
                $newTo = Auth::user()->accounts()->find(
                    $transfer->accountto_id
                );
                $oldTo->currentbalance -= floatval(
                    $transfer->getOriginal('amount')
                );
                $newTo->currentbalance += floatval($transfer->amount);
                $oldTo->save();
                $newTo->save();

                // update or create balancemodifier for OLD TO account.
                $balanceModifier = $oldTo->balancemodifiers()->where(
                    'date', $transfer->date
                )->first();
                if (is_null($balanceModifier)) {
                    $balanceModifier = new Balancemodifier;
                    $balanceModifier->account()->associate($toAccount);
                    $balanceModifier->balance = 0;
                    $balanceModifier->date = $transfer->date;
                }
                // add it:
                $balanceModifier->balance -= floatval(
                    $transfer->getOriginal('amount')
                );;
                $balanceModifier->save();
                unset($balanceModifier);

                // update or create balancemodifier for NEW TO account:
                $balanceModifierFrom = $newTo->balancemodifiers()->where(
                    'date', $transfer->date
                )->first();
                if (is_null($balanceModifierFrom)) {
                    $balanceModifierFrom = new Balancemodifier;
                    $balanceModifierFrom->account()->associate($toAccount);
                    $balanceModifierFrom->balance = 0;
                    $balanceModifierFrom->date = $transfer->date;
                }
                // add it:
                $balanceModifierFrom->balance += floatval($transfer->amount);
                $balanceModifierFrom->save();
            }

            // so something complicated has changed!
            // first, we add the original amount back to the "from" account.
        }

        return null;
    }

    /**
     * Triggers on the deletion of a Transfer.
     *
     * @param Transfer $transfer
     */
    public function deleteTransfer(Transfer $transfer)
    {
        Cache::flush();
        $accountFrom = $transfer->accountfrom()->first();
        $accountTo = $transfer->accountto()->first();
        // add amounts back and from the relevant accounts
        $accountFrom->currentbalance += $transfer->amount;
        $accountTo->currentbalance -= $transfer->amount;
        $accountFrom->save();
        $accountTo->save();

        // update or create balancemodifier for TO account.
        $balanceModifier = $accountTo->balancemodifiers()->where(
            'date', $transfer->date
        )->first();
        if (is_null($balanceModifier)) {
            $balanceModifier = new Balancemodifier;
            $balanceModifier->account()->associate($accountTo);
            $balanceModifier->balance = 0;
            $balanceModifier->date = $transfer->date;
        }
        // add it:
        $balanceModifier->balance -= $transfer->amount;
        $balanceModifier->save();
        unset($balanceModifier);

        // update or create balancemodifier for FROM account:
        $bmf = $accountFrom->balancemodifiers()->where('date', $transfer->date)
            ->first();
        if (is_null($bmf)) {
            $bmf = new Balancemodifier;
            $bmf->account()->associate($accountFrom);
            $bmf->balance = 0;
            $bmf->date = $transfer->date;
        }
        // add it:
        $bmf->balance += $transfer->amount;
        $bmf->save();
    }

    /**
     * Listen to the triggers.
     *
     * @param $events
     *
     */
    public function subscribe(Illuminate\Events\Dispatcher $events)
    {
        $events->listen(
            'eloquent.creating: Transfer', 'TransferTrigger@createTransfer'
        );
        $events->listen(
            'eloquent.updating: Transfer', 'TransferTrigger@editTransfer'
        );
        $events->listen(
            'eloquent.deleted: Transfer', 'TransferTrigger@deleteTransfer'
        );
    }

}

$subscriber = new TransferTrigger;

Event::subscribe($subscriber);
