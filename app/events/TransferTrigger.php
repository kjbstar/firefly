<?php
use Carbon\Carbon as Carbon;

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
     * Triggers on the editing of a transfer. It's virtually
     * the same as the trigger for a transaction:
     *
     * Firstly, the date must be after any of the two account's creation
     * date. Will return false if this is not the case.
     *
     * If the two accounts are the same account, return false as well.
     *
     * If either of the two accounts has changed, trigger on that.
     *
     * If the date of the transfer has changed, trigger on that.
     *
     * If the amount has changed, trigger on that.
     *
     * @param Transfer $transfer
     *
     * @return bool
     */
    public function editTransfer(Transfer $transfer)
    {
        $accountFrom = $transfer->accountfrom()->first();
        $accountTo = $transfer->accountto()->first();
        $oldDate = new Carbon($transfer->getOriginal('date'));
        if ($transfer->date < $accountFrom->openingbalancedate
            || $transfer->date < $accountTo->openingbalancedate
        ) {
            return false;
        }
        if ($accountFrom->id == $accountTo->id) {
            return false;
        }

        if ($accountFrom->id != intval($transfer->getOriginal('accountfrom_id'))
        ) {
            $this->triggerAccountFromChanged($transfer);
        }

        if ($accountTo->id != intval($transfer->getOriginal('accountto_id'))) {
            $this->triggerAccountToChanged($transfer);
        }

        if ($transfer->date != $oldDate) {
            $this->triggerDateChanged($transfer);
        }
        if ($transfer->amount != floatval($transfer->getOriginal('amount'))) {
            $this->triggerAmountChanged($transfer);
        }
        return true;
    }

    /**
     * The original amount can be "put back" onto the old account,
     * and instead be substracted from the new account.
     *
     * Also update the balance modifiers that are part of this day.
     *
     * @param Transfer $transfer
     */
    private function triggerAccountFromChanged(Transfer $transfer)
    {
        $oldAccount = Auth::user()->accounts()->find(
            $transfer->getOriginal('accountfrom_id')
        );
        $newAccount = $transfer->accountfrom()->first();
        $date = new Carbon($transfer->getOriginal('date'));

        $oldBm = $oldAccount->balancemodifiers()->onDay($date)->first();
        if (is_null($oldBm)) {
            $oldBm = new Balancemodifier();
            $oldBm->account()->associate($oldAccount);
            $oldBm->date = $date;
            $oldBm->balance = 0;
        }
        $oldBm->balance += floatval($transfer->getOriginal('amount'));
        $oldAccount->currentbalance += floatval(
            $transfer->getOriginal('amount')
        );
        $oldBm->save();
        $oldAccount->save();

        // remove from new account instead:
        $newBm = $newAccount->balancemodifiers()->onDay($date)->first();
        if (is_null($newBm)) {
            $newBm = new Balancemodifier();
            $newBm->account()->associate($newAccount);
            $newBm->date = $date;
            $newBm->balance = 0;
        }
        $newBm->balance -= floatval($transfer->getOriginal('amount'));
        $newAccount->currentbalance -= floatval(
            $transfer->getOriginal('amount')
        );
        $newBm->save();
        $newAccount->save();

    }

    /**
     * It's no longer being transfered to the 'old' account,
     * there is a 'new' one. So the old account 'loses' the money while the
     * new account gains it.
     *
     * @param Transfer $transfer
     */
    private function triggerAccountToChanged(Transfer $transfer)
    {
        $oldAccount = Auth::user()->accounts()->find(
            $transfer->getOriginal('accountto_id')
        );
        $newAccount = $transfer->accountto()->first();
        $date = new Carbon($transfer->getOriginal('date'));

        $oldBm = $oldAccount->balancemodifiers()->onDay($date)->first();
        if (is_null($oldBm)) {
            $oldBm = new Balancemodifier();
            $oldBm->account()->associate($oldAccount);
            $oldBm->date = $date;
            $oldBm->balance = 0;
        }
        $oldBm->balance -= floatval($transfer->getOriginal('amount'));
        $oldAccount->currentbalance -= floatval(
            $transfer->getOriginal('amount')
        );
        $oldBm->save();
        $oldAccount->save();

        // add to new account instead:
        $newBm = $newAccount->balancemodifiers()->onDay($date)->first();
        if (is_null($newBm)) {
            $newBm = new Balancemodifier();
            $newBm->account()->associate($newAccount);
            $newBm->date = $date;
            $newBm->balance = 0;
        }
        $newBm->balance += floatval($transfer->getOriginal('amount'));
        $newAccount->currentbalance += floatval(
            $transfer->getOriginal('amount')
        );
        $newBm->save();
        $newAccount->save();

    }

    /**
     * When the date changes, we update four balance modifiers:
     * - two for the account the money came from
     * -- the original we put the money "back"
     * -- the new one we remove the money from.
     *- two for the account the money went to
     * -- the original we remove the money from
     * -- the new one we put it into.
     *
     * @param $transfer
     */
    private function triggerDateChanged(Transfer $transfer)
    {
        $accountFrom = $transfer->accountfrom()->first();
        $accountTo = $transfer->accountto()->first();
        $oldDate = new Carbon($transfer->getOriginal('date'));

        // first put the money back on the old date:
        $balanceModifier = $accountFrom->balancemodifiers()->onDay($oldDate)
            ->first();
        if (is_null($balanceModifier)) {
            $balanceModifier = new Balancemodifier();
            $balanceModifier->account()->associate($accountFrom);
            $balanceModifier->date = $oldDate;
            $balanceModifier->balance = 0;
        }
        $balanceModifier->balance += $transfer->getOriginal('amount');
        $balanceModifier->save();
        unset($balanceModifier);

        // then remove it form the new date:
        $balanceModifier = $accountFrom->balancemodifiers()->onDay(
            $transfer->date
        )->first();
        if (is_null($balanceModifier)) {
            $balanceModifier = new Balancemodifier();
            $balanceModifier->account()->associate($accountFrom);
            $balanceModifier->date = $transfer->date;
            $balanceModifier->balance = 0;
        }
        $balanceModifier->balance -= $transfer->getOriginal('amount');
        $balanceModifier->save();

        // update the new account. same as above, but in reverse.
        // remove the money from the old date. its no longer received on
        // that date
        $balanceModifier = $accountTo->balancemodifiers()->onDay($oldDate)
            ->first();
        if (is_null($balanceModifier)) {
            $balanceModifier = new Balancemodifier();
            $balanceModifier->account()->associate($accountTo);
            $balanceModifier->date = $oldDate;
            $balanceModifier->balance = 0;
        }
        $balanceModifier->balance -= floatval(
            $transfer->getOriginal('amount')
        );
        $balanceModifier->save();
        unset($balanceModifier);

        // update the new date's balancemodifier. On that date,
        // we receive the amount.

        $balanceModifier = $accountTo->balancemodifiers()->onDay(
            $transfer->date
        )->first();
        if (is_null($balanceModifier)) {
            $balanceModifier = new Balancemodifier();
            $balanceModifier->account()->associate($accountTo);
            $balanceModifier->date = $transfer->date;
            $balanceModifier->balance = 0;
        }
        $balanceModifier->balance += floatval(
            $transfer->getOriginal('amount')
        );
        $balanceModifier->save();
        unset($balanceModifier);
    }

    /**
     * If the amount has changed we have to update two
     * balancemodifiers. The account the money comes from
     * "loses" the difference. The account the money went to "gains" the
     * difference. Also, the account's balances must be updated.
     *
     *
     * @param Transfer $transfer
     */
    private function triggerAmountChanged(Transfer $transfer)
    {
        $accountFrom = $transfer->accountfrom()->first();
        $accountTo = $transfer->accountto()->first();
        $diff = $transfer->amount - floatval($transfer->getOriginal('amount'));

        $balanceModifier = $accountFrom->balancemodifiers()->onDay(
            $transfer->date
        )->first();
        if (is_null($balanceModifier)) {
            $balanceModifier = new Balancemodifier();
            $balanceModifier->account()->associate($accountFrom);
            $balanceModifier->date = $transfer->date;
            $balanceModifier->balance = 0;
        }
        $balanceModifier->balance -= $diff;
        $accountFrom->currentbalance -= $diff;
        $balanceModifier->save();
        $accountFrom->save();
        unset($balanceModifier);

        // reverse for the other account
        $balanceModifier = $accountTo->balancemodifiers()->onDay(
            $transfer->date
        )->first();
        if (is_null($balanceModifier)) {
            $balanceModifier = new Balancemodifier();
            $balanceModifier->account()->associate($accountTo);
            $balanceModifier->date = $transfer->date;
            $balanceModifier->balance = 0;
        }
        $balanceModifier->balance += $diff;
        $accountTo->currentbalance += $diff;
        $balanceModifier->save();
        $accountTo->save();
        unset($balanceModifier);

    }

////        // no changes in account ID's:
////        if ($transfer->getOriginal('accountfrom_id')
////            == $transfer->accountfrom_id
////            && $transfer->getOriginal('accountto_id') == $transfer->accountto_id
////        ) {
////            // slechts amount aangepast?
////            if (floatval($transfer->getOriginal('amount')) != floatval(
////                    $transfer->amount
////                )
////            ) {
////                $diff = floatval($transfer->amount) - floatval(
////                        $transfer->getOriginal('amount')
////                    );
////                $fromAccount = $transfer->accountfrom()->first();
////                $toAccount = $transfer->accountto()->first();
////                // add to to_account
////                // sub from from_account
////                $toAccount->currentbalance += $diff;
////                $fromAccount->currentbalance -= $diff;
////                $toAccount->save();
////                $fromAccount->save();
////
////                // update or create balancemodifier for TO account.
////                $balanceModifier = $toAccount->balancemodifiers()->where(
////                    'date', $transfer->date
////                )->first();
////                if (is_null($balanceModifier)) {
////                    $balanceModifier = new Balancemodifier;
////                    $balanceModifier->account()->associate($toAccount);
////                    $balanceModifier->balance = 0;
////                    $balanceModifier->date = $transfer->date;
////                }
////                // add it:
////                $balanceModifier->balance += $diff;
////                $balanceModifier->save();
////                unset($balanceModifier);
////
////                // update or create balancemodifier for FROM account:
////                $balanceModifierFrom = $fromAccount->balancemodifiers()->where(
////                    'date', $transfer->date
////                )->first();
////                if (is_null($balanceModifierFrom)) {
////                    $balanceModifierFrom = new Balancemodifier;
////                    $balanceModifierFrom->account()->associate($fromAccount);
////                    $balanceModifierFrom->balance = 0;
////                    $balanceModifierFrom->date = $transfer->date;
////                }
////                // add it:
////                $balanceModifierFrom->balance -= $diff;
////                $balanceModifierFrom->save();
////
////
////                return true;
////            }
////        } else {
////
////            // if the from account has changed:
////            if ($transfer->getOriginal('accountfrom_id')
////                != $transfer->accountfrom_id
////            ) {
////                // we put the OLD amount back in the from account and sub it from the new
////                // from account. Also the NEW amount (if applicable, we dont care).
////                $oldFrom = Auth::user()->accounts()->find(
////                    $transfer->getOriginal('accountfrom_id')
////                );
////                $newFrom = Auth::user()->accounts()->find(
////                    $transfer->accountfrom_id
////                );
////                $oldFrom->currentbalance += floatval(
////                    $transfer->getOriginal('amount')
////                );
////                $newFrom->currentbalance -= floatval($transfer->amount);
////                $oldFrom->save();
////                $newFrom->save();
////
////                // update or create balancemodifier for OLD FROM account.
////                $balanceModifier = $oldFrom->balancemodifiers()->where(
////                    'date', $transfer->date
////                )->first();
////                if (is_null($balanceModifier)) {
////                    $balanceModifier = new Balancemodifier;
////                    $balanceModifier->account()->associate($oldFrom);
////                    $balanceModifier->balance = 0;
////                    $balanceModifier->date = $transfer->date;
////                }
////                // add it:
////                $balanceModifier->balance += floatval(
////                    $transfer->getOriginal('amount')
////                );;
////                $balanceModifier->save();
////                unset($balanceModifier);
////
////                // update or create balancemodifier for NEW FROM account:
////                $balanceModifierFrom = $newFrom->balancemodifiers()->where(
////                    'date', $transfer->date
////                )->first();
////                if (is_null($balanceModifierFrom)) {
////                    $balanceModifierFrom = new Balancemodifier;
////                    $balanceModifierFrom->account()->associate($oldTo);
////                    $balanceModifierFrom->balance = 0;
////                    $balanceModifierFrom->date = $transfer->date;
////                }
////                // add it:
////                $balanceModifierFrom->balance -= floatval($transfer->amount);
////                $balanceModifierFrom->save();
////            }
////            // if the TO account has changed:
////            if ($transfer->getOriginal('accountto_id')
////                != $transfer->accountto_id
////            ) {
////                // we take the old amount back from the old TO account
////                // and give the new amount to the new to account
////
////                $oldTo = Auth::user()->accounts()->find(
////                    $transfer->getOriginal('accountto_id')
////                );
////                $newTo = Auth::user()->accounts()->find(
////                    $transfer->accountto_id
////                );
////                $oldTo->currentbalance -= floatval(
////                    $transfer->getOriginal('amount')
////                );
////                $newTo->currentbalance += floatval($transfer->amount);
////                $oldTo->save();
////                $newTo->save();
////
////                // update or create balancemodifier for OLD TO account.
////                $balanceModifier = $oldTo->balancemodifiers()->where(
////                    'date', $transfer->date
////                )->first();
////                if (is_null($balanceModifier)) {
////                    $balanceModifier = new Balancemodifier;
////                    $balanceModifier->account()->associate($toAccount);
////                    $balanceModifier->balance = 0;
////                    $balanceModifier->date = $transfer->date;
////                }
////                // add it:
////                $balanceModifier->balance -= floatval(
////                    $transfer->getOriginal('amount')
////                );;
////                $balanceModifier->save();
////                unset($balanceModifier);
////
////                // update or create balancemodifier for NEW TO account:
////                $balanceModifierFrom = $newTo->balancemodifiers()->where(
////                    'date', $transfer->date
////                )->first();
////                if (is_null($balanceModifierFrom)) {
////                    $balanceModifierFrom = new Balancemodifier;
////                    $balanceModifierFrom->account()->associate($toAccount);
////                    $balanceModifierFrom->balance = 0;
////                    $balanceModifierFrom->date = $transfer->date;
////                }
////                // add it:
////                $balanceModifierFrom->balance += floatval($transfer->amount);
////                $balanceModifierFrom->save();
////            }
////
////            // so something complicated has changed!
////            // first, we add the original amount back to the "from" account.
////        }
//
//        return null;
//    }

    /**
     * Triggers on the deletion of a Transfer.
     *
     * @param Transfer $transfer
     */
    public function deleteTransfer(Transfer $transfer)
    {
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
