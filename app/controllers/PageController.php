<?php

/**
 * Class PageController
 */
class PageController extends BaseController
{
    /**
     * Shows a list of transactions with similar
     * budgets and categories.
     *
     * @return View
     */
    /*
    public function refineTransactions()
    {
        $list = [];
        $transactions = Auth::user()->transactions()->with(
            ['components' => function ($query) {
                    $query->whereIn('type', ['category', 'budget']);
                }]
        )->get();
        foreach ($transactions as $t) {
            $budget = null;
            $category = null;
            foreach ($t->components as $c) {
                if ($c->type === 'budget') {
                    $budget = $c->name;
                }
                if ($c->type === 'category') {
                    $category = $c->name;
                }
            }
            if (!is_null($category) && !is_null($budget)
                && $category == $budget
            ) {
                $t->cat = $category;
                $t->bud = $budget;
                $list[] = $t;
            }
        }


        return View::make('home.refine')->with('list', $list);
    }
    */

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
            $first = $account->balancemodifiers()->where(
                'date', $account->openingbalancedate->format('Y-m-d')
            )->first();
            if (!$first) {
                $first = new Balancemodifier;
            }
            $first->account()->associate($account);
            $first->date = $account->openingbalancedate;
            $first->balance = $account->openingbalance;
            $first->save();
            // loop all transactions and create / update balance modifiers:
            foreach (
                $account->transactions()->orderBy('date', 'ASC')->get() as $t
            ) {
                $bm = $account->balancemodifiers()->where(
                    'date', $t->date->format('Y-m-d')
                )->first();
                if (!$bm) {
                    $bm = new Balancemodifier;
                    $bm->account()->associate($account);
                    $bm->date = $t->date;
                    $bm->balance = 0;
                }

                $bm->balance += floatval($t->amount);
                $bm->save();
//
                // update currentbalance
                $account->currentbalance += floatval($t->amount);
                $account->save();
            }
//             now loop all transfer to's, same routine:
            foreach ($account->transfersto as $t) {
                $bm = $account->balancemodifiers()->where(
                    'date', $t->date->format('Y-m-d')
                )->first();
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
                $bm = $account->balancemodifiers()->where(
                    'date', $t->date->format('Y-m-d')
                )->first();
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

    /**
     * An old helper function to help me get some missing transactions fixed.
     *
     * @return \Illuminate\View\View
     */
    /*
    public function compare()
    {

        $row = 1;
        $result = [];
        if (($handle = fopen(
                "/Library/WebServer/Documents/compare/transactions.txt", "r"
            )) !== false
        ) {
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $date = new Carbon($data[7]);
                $otherDate = new Carbon($data[2]);
                $current = [];
                $current['date'] = $date;
                $current['otherdate'] = $otherDate;
                $amount = floatval($data[4]);
                $current['amount'] = $amount;
                $current['descr'] = $data[10] . ' ' . $data[11];
                $num = count($data);

                // find transaction?
                $transaction = Auth::user()->transactions()->where(
                    function ($q) use ($date, $otherDate) {
                        $q->where('date', $date->format('Y-m-d'));
                        $q->orWhere('date', $otherDate->format('Y-m-d'));
                    }

                )->where(
                        function ($q) use ($amount) {
                            $q->where('amount', $amount);
                            $q->orWhere('amount', $amount * -1);
                        }
                    )->first();
                if ($transaction) {
                    $current['transaction'] = $transaction;
                } else {
                    $current['transaction'] = false;
                }


                //echo "<p> $num fields in line $row: <br /></p>\n";
                $row++;
                for ($c = 0; $c < $num; $c++) {
                    //   echo $c . ': ' . $data[$c] . "<br />\n";
                }
                $result[] = $current;
            }
            fclose($handle);
        }

        return View::make('home.compare')->with('data', $result);
    }
    */
} 