<?php

use Illuminate\Encryption\DecryptException as DecryptException;

/**
 * Class PageController
 */
class PageController extends BaseController
{

    /**
     * Flushes the cache.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function flush()
    {
        Cache::flush();
        Cache::userFlush();
        return Redirect::to('/');
    }

    /**
     * Recalculated EVERY balancemodifier.
     */
    public function recalculate()
    {
        Cache::flush();
        $accounts = Auth::user()->accounts()->get();

        foreach ($accounts as $account) {
            // delete ALL balancemodifiers.
            Balancemodifier::where('account_id', $account->id)->delete();
            // reset the current balance.
            $account->currentbalance = $account->openingbalance;
            $account->save();
            // create the FIRST balance modifier:
            $first = $account->balancemodifiers()->where('date', $account->openingbalancedate->format('Y-m-d'))->first(
            );
            if (!$first) {
                $first = new Balancemodifier;
            }
            $first->account()->associate($account);
            $first->date = $account->openingbalancedate;
            $first->balance = $account->openingbalance;
            $first->save();

            // loop all transactions and create / update balance modifiers:
            foreach ($account->transactions()->orderBy('date', 'ASC')->get() as $t) {
                $bm = $account->balancemodifiers()->where('date', $t->date->format('Y-m-d'))->first();
                if (!$bm) {
                    $bm = new Balancemodifier;
                    $bm->account()->associate($account);
                    $bm->date = $t->date;
                    $bm->balance = 0;
                }

                $bm->balance += floatval($t->amount);
                $bm->save();

                // update currentbalance
                $account->currentbalance += floatval($t->amount);
                $account->save();
            }
//             now loop all transfer to's, same routine:
            foreach ($account->transfersto as $t) {
                $bm = $account->balancemodifiers()->where('date', $t->date->format('Y-m-d'))->first();
                if (!$bm) {
                    $bm = new Balancemodifier;
                    $bm->account()->associate($account);
                    $bm->date = $t->date;
                    $bm->balance = 0;
                }
                $bm->balance += floatval($t->amount);
                $bm->save();
                // update currentbalance
                $account->currentbalance += floatval($t->amount);
                $account->save();
            }

            // and the transfers FROM:
            foreach ($account->transfersfrom as $t) {
                $bm = $account->balancemodifiers()->where('date', $t->date->format('Y-m-d'))->first();
                if (!$bm) {
                    $bm = new Balancemodifier;
                    $bm->account()->associate($account);
                    $bm->date = $t->date;
                    $bm->balance = 0;
                }
                $bm->balance -= floatval($t->amount);
                $bm->save();
                // update currentbalance
                $account->currentbalance -= floatval($t->amount);
                $account->save();
            }
        }

        return Redirect::to('/');
    }

    public function decrypt()
    {
        set_time_limit(0);
        // decrypt ACCOUNTS.
        $accounts = Account::get();
        foreach ($accounts as $account) {
            try {
                $name = Crypt::decrypt($account->name);
                $account->name = $name;
                $account->save();
                echo 'Account '.$account->name.' decrypted.<br>';
            } catch (DecryptException $e) {
                echo 'Account '.$account->name.' already decrypted!<br>';
            }
        }

        // decrypt COMPONENTS
        $items = Component::get();
        foreach ($items as $item) {
            try {
                $name = Crypt::decrypt($item->name);
                $item->name = $name;
                $item->save();
                echo 'Component '.$item->name.' decrypted.<br>';
            } catch (DecryptException $e) {
                echo 'Component '.$item->name.' already decrypted!<br>';
            }
        }

        // decrypt PIGGY BANKS
        $items = Piggybank::get();
        foreach ($items as $item) {
            try {
                $name = Crypt::decrypt($item->name);
                $item->name = $name;
                $item->save();
                echo 'Piggy '.$item->name.' decrypted.<br>';
            } catch (DecryptException $e) {
                echo 'Piggy '.$item->name.' already decrypted!<br>';
            }
        }

        // decrypt PREDICTABLES
        $items = Predictable::get();
        foreach ($items as $item) {
            try {
                $name = Crypt::decrypt($item->description);
                $item->description = $name;
                $item->save();
                echo 'Predictable '.$item->description.' decrypted.<br>';
            } catch (DecryptException $e) {
                echo 'Predictable '.$item->description.' already decrypted!<br>';
            }
        }

        // decrypt TRANSACTIONS
        $items = Transaction::get();
        foreach ($items as $item) {
            try {
                $name = Crypt::decrypt($item->description);
                $item->description = $name;
                $item->save();
                echo 'Transaction '.$item->description.' decrypted.<br>';
            } catch (DecryptException $e) {
                echo 'Transaction '.$item->description.' already decrypted!<br>';
            }
        }

        // decrypt TRANSFERS
        $items = Transfer::get();
        foreach ($items as $item) {
            try {
                $name = Crypt::decrypt($item->description);
                $item->description = $name;
                $item->save();
                echo 'Transfer '.$item->description.' decrypted.<br>';
            } catch (DecryptException $e) {
                echo 'Transfer '.$item->description.' already decrypted!<br>';
            }
        }
    }

    public function moveComponents() {
        $components = Auth::user()->components()->get();

        $benType = Type::where('type','beneficiary')->first();
        $budType = Type::where('type','budget')->first();
        $catType = Type::where('type','category')->first();


        /** @var $c Component */
        foreach($components as $c) {
            echo $c->name.' [old: '.$c->type.'] ';// [new: '.$c->type()->first()->type.']<br>';
            if($c->type != $c->type()->first()->type) {
                echo '<span style="color:red">no match</span>';
                switch($c->type) {
                    default:
                        echo 'no type';
                        exit;
                    case 'budget':
                        $c->type()->associate($budType);
                        $c->save();
                        break;
                    case 'category':
                        $c->type()->associate($catType);
                        $c->save();
                        break;
                    case 'beneficiary':

                        break;
                }
            } else {
                echo '<span style="color:green">match</span>';


            }
            echo '<br>';

        }
    }
}