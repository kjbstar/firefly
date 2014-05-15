<?php
use Carbon\Carbon as Carbon;

/**
 * Class AccountTrigger
 */
class AccountTrigger
{

    /**
     * When an account is edited, various things may happen.
     *
     * If the opening balance date has changed into the future,
     * BM's before that date must be removed and the one ON
     * that new date must be updated.
     *
     * If the opening balance date has changed into the past,
     * new BM's must be created onward from the new date to the old date,
     * and the one ON the exact old date must be updated (the balance as
     * it was must be removed from it).
     *
     * If the amount has changed, the BM of that date must be updated.
     *
     * First we check for date changes, and THEN for amount changes,
     * because the amount changes can be implemented after the date has been
     * changed.
     *
     * @param Account $account
     *
     * @return boolean
     */
    public function editAccount(Account $account)
    {
        $result = true;
        if ($this->validateAccountName($account)) {
            $originalDate = new Carbon($account->getOriginal(
                'openingbalancedate'
            ));
            if ($account->openingbalancedate < $originalDate) {
                $result = $this->triggerAccountDateToPast($account);
            } else {
                if ($account->openingbalancedate > $originalDate) {
                    $result = $this->triggerAccountDateToFuture($account);
                }
            }
            if($result === false) {
                return false;
            }
            if (floatval($account->openingbalance) != floatval($account->getOriginal('openingbalance'))) {
                $result = $this->triggerAccountAmountChanged($account);
            }
        } else {
            return false;
        }

        return $result;


    }

    /**
     * When the account's opening date has been changed to a date in the
     * past, new BM's must be created onward from the new date (in the past) to
     * the old date ("now" in this reference), and the one ON the exact old
     * date must be updated (the balance as it was must be removed from it).
     *
     * If any BM's do not exist they will be created.
     *
     * @param Account $account
     *
     * @return boolean
     */
    public function triggerAccountDateToPast(Account $account)
    {
        $balance = floatval($account->getOriginal('openingbalance'));
        $start = $account->openingbalancedate;
        $oldDate = new Carbon($account->getOriginal('openingbalancedate'));
        $end = clone $oldDate;
        $end->subDay();
        $current = clone $start;

        while ($current <= $end) {

            // delete if exists. should not exist!
            $account->balancemodifiers()->onDay($current)->delete();

            // create it.
            $balanceModifier = new Balancemodifier();
            $balanceModifier->date = $current;
            if ($current == $account->openingbalancedate) {
                $balanceModifier->balance = floatval($balance);
            } else {
                $balanceModifier->balance = 0.0;
            }
            $balanceModifier->account()->associate($account);
            $balanceModifier->save();
            $current->addDay();
        }
        unset($balanceModifier);
        $end->addDay();
        $balanceModifier = $account->balancemodifiers()->onDay($end)->first();
        if (is_null($balanceModifier)) {
            Session::flash('error_extended','Found a weird database inconsistency. Cancelling!');
            return false;
        } else {
            $balanceModifier->balance -= floatval($balance);
            $balanceModifier->save();
        }

        return true;
    }

    /**
     * If the opening balance date has changed into the future,
     * BM's before that date must be removed and the one ON
     * that new date must be updated.
     *
     * @param Account $account
     *
     * @return boolean
     */
    private function triggerAccountDateToFuture(Account $account)
    {
        $balance = floatval($account->getOriginal('openingbalance'));
        $newDate = $account->openingbalancedate;
        $account->balancemodifiers()->beforeDate($newDate)->delete();

        // update one on new date (if it exists, it doesn't have to).
        $balanceModifier = $account->balancemodifiers()->onDay($newDate)->first();
        if (is_null($balanceModifier)) {
            $balanceModifier = new Balancemodifier();
            $balanceModifier->account()->associate($account);
            $balanceModifier->date = $newDate;
            $balanceModifier->balance = 0;
        }
        $balanceModifier->balance += $balance;
        $balanceModifier->save();
        return true;
    }

    /**
     * If the amount has changed, the BM of that date must be updated.
     *
     * @param Account $account
     *
     * @return boolean
     */
    private function triggerAccountAmountChanged(Account $account)
    {
        $oldBalance = floatval($account->getOriginal('openingbalance'));
        $newBalance = floatval($account->openingbalance);
        $difference = $newBalance - $oldBalance;
        $balanceModifier = $account->balancemodifiers()->onDay(
            $account->openingbalancedate
        )->first();
        // update the account itself, add the difference of
        // this change:


        // THERE SHOULD BE A BALANCE MODIFIER
        if (is_null($balanceModifier)) {
            Session::flash('error_extended','Found a weird database inconsistency. Sorry!');
            return false;
        } else {
            $balanceModifier->balance += $difference;
            $balanceModifier->save();
        }

        return true;
    }

    /**
     * On the creation of a new account, a new balance modifier is created as
     * well, which will help various charts display their information.
     *
     * @param Account $account
     *
     * @return boolean
     */
    public function createAccount(Account $account)
    {
        // create new BM for that day (if necessary).
        if ($this->validateAccountName($account)) {
            $count = Balancemodifier::whereAccountId($account->id)->whereDate($account->openingbalancedate)->count();
            if ($count == 0) {
                $balanceModifier = new Balancemodifier;
                $balanceModifier->date = $account->openingbalancedate;
                $balanceModifier->account()->associate($account);
                $balanceModifier->balance = $account->openingbalance;
                $balanceModifier->save();
            }
            return true;
        }
        return false;
    }

    /**
     * @param Account $account
     *
     * @return bool
     */
    public function validateAccountName(Account $account)
    {
        $user = Auth::user();
        if (is_null(Auth::user())) {
            $user = User::find($account->user_id);
        }
        if (is_null($user)) {
            Session::flash('error_extended','Found no user!');
            return false;
        }
        if (is_null($account->id)) {
            $accounts = $user->accounts()->get();
        } else {
            $accounts = $user->accounts()->where('id', '!=', $account->id)->get();
        }

        foreach ($accounts as $dbAccount) {
            if ($dbAccount->name == $account->name) {
                Session::flash('error_extended','Accout name "'.$account->name.'" is already used.');
                return false;
            }
        }

        return true;
    }

    /**
     * Make the triggers.
     *
     * @param \Illuminate\Events\Dispatcher $events
     */
    public function subscribe(Illuminate\Events\Dispatcher $events)
    {
        $events->listen('eloquent.created: Account', 'AccountTrigger@createAccount');
        $events->listen('eloquent.updating: Account', 'AccountTrigger@editAccount');
        // validate the name of the (new) account:
        $events->listen('eloquent.creating: Account', 'AccountTrigger@validateAccountName');
    }

}
