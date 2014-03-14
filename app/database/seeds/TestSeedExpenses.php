<?php

class TestSeedExpenses extends Seeder
{

    public function run()
    {
        DB::table('transactions')->delete();
        $user = User::first();

        $today = new Carbon\Carbon;
        $twoYears = clone $today;
        $twoYears->subYears(2);
        $current = clone $twoYears;
        $count = 0;
        while ($current < $today) {
            $count++;
            for ($i = 0; $i < 3; $i++) {
                $account = Account::orderBy(DB::Raw('RAND()'))->first();
                $amount = rand(-1, -200);
                $day = rand(10, 28);
                $setObjects = rand(1,4);

                $t = Transaction::create(
                    ['user_id'          => $user->id,
                     'account_id'       => $account->id,
                     'description'      => 'Test Transaction #' . $count,
                     'amount'           => $amount,
                     'date'             => $current->format('Y-m-').$day,
                     'ignoreprediction' => 0, 'ignoreallowance' => 0,
                     'mark'             => 0]
                );
                switch($setObjects) {
                    case 1:
                        // set beneficiary
                        $beneficiary = Component::orderBy(DB::Raw('RAND()'))->first();
                        $t->components()->save($beneficiary);

                        break;
                    case 2:
                        // set a budget and a category
                        $budget = Component::orderBy(DB::Raw('RAND()'))->first();
                        $category = Component::orderBy(DB::Raw('RAND()'))->first();
                        $t->components()->save($budget);
                        $t->components()->save($category);
                        break;
                    case 3:
                        // set all.
                        $budget = Component::orderBy(DB::Raw('RAND()'))->first();
                        $category = Component::orderBy(DB::Raw('RAND()'))->first();
                        $beneficiary = Component::orderBy(DB::Raw('RAND()'))->first();
                        $t->components()->save($beneficiary);
                        $t->components()->save($budget);
                        $t->components()->save($category);
                        break;
                    case 4:
                        // set none.
                        break;
                }


            }

            $current->addMonth();
        }
    }
} 