<?php
/**
 * Created by PhpStorm.
 * User: sander
 * Date: 10/12/13
 * Time: 09:32
 */

class PageController extends BaseController
{
    /**
     * Shows a list of transactions with similar
     * budgets and categories.
     *
     * @return View
     */
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
} 