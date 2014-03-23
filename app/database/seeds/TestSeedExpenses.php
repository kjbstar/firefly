<?php

class TestSeedExpenses extends Seeder
{

    public function run()
    {
        DB::table('transactions')->delete();
        $user = User::first();

        $today = new Carbon\Carbon;
        $today->subMonth();
        $twoYears = clone $today;
        $twoYears->subYears(2);
        $current = clone $twoYears;
        $count = 0;

        $mainAccount = $user->accounts()->first();

        // create a transaction with components
        // for test purposes:
        $filled = Transaction::create(
            ['user_id'          => $user->id,
             'account_id'       => $mainAccount->id,
             'description'      => 'Something with components',
             'amount'           => 0.01,
             'date'             => $current->format('Y-m-') . '12',
             'ignoreprediction' => 0,
             'ignoreallowance'  => 0,
             'mark'             => 0]
        );

        $beneficiary = $user->components()->where('type', 'beneficiary')->first();
        $category = $user->components()->where('type', 'category')->first();
        $budget = $user->components()->where('type', 'budget')->first();

        $filled->components()->save($beneficiary);
        $filled->components()->save($category);
        $filled->components()->save($budget);


        // loop every month:
        while ($current < $today) {
            // rent on the first of the month.
            $rent = Transaction::create(
                ['user_id'          => $user->id,
                 'account_id'       => $mainAccount->id,
                 'description'      => 'Rent',
                 'amount'           => -500,
                 'date'             => $current->format('Y-m-') . '01',
                 'ignoreprediction' => 0,
                 'ignoreallowance'  => 0,
                 'mark'             => 0]
            );
            // insurance.
            $insurance = Transaction::create(
                ['user_id'          => $user->id,
                 'account_id'       => $mainAccount->id,
                 'description'      => 'Insurance',
                 'amount'           => -120,
                 'date'             => $current->format('Y-m-') . '02',
                 'ignoreprediction' => 0,
                 'ignoreallowance'  => 0,
                 'mark'             => 0]
            );
            // phone bill
            $phone = Transaction::create(
                ['user_id'          => $user->id,
                 'account_id'       => $mainAccount->id,
                 'description'      => 'Phone bill',
                 'amount'           => -40,
                 'date'             => $current->format('Y-m-') . '03',
                 'ignoreprediction' => 0,
                 'ignoreallowance'  => 0,
                 'mark'             => 0]
            );
            // internet
            $internet = Transaction::create(
                ['user_id'          => $user->id,
                 'account_id'       => $mainAccount->id,
                 'description'      => 'Internet',
                 'amount'           => -45,
                 'date'             => $current->format('Y-m-') . '03',
                 'ignoreprediction' => 0,
                 'ignoreallowance'  => 0,
                 'mark'             => 0]
            );
            // Gas and power
            $gaspower = Transaction::create(
                ['user_id'          => $user->id,
                 'account_id'       => $mainAccount->id,
                 'description'      => 'Gas and power',
                 'amount'           => -140,
                 'date'             => $current->format('Y-m-') . '03',
                 'ignoreprediction' => 0,
                 'ignoreallowance'  => 0,
                 'mark'             => 0]
            );

            // (almost) every day, we buy cigarettes:
            for ($i = 1; $i <= $current->format('t'); $i++) {
                $d = sprintf('%02d', $i);
                $cigarettes = Transaction::create(
                    ['user_id'          => $user->id,
                     'account_id'       => $mainAccount->id,
                     'description'      => 'Cigarettes',
                     'amount'           => -5.5,
                     'date'             => $current->format('Y-m-') . $d,
                     'ignoreprediction' => 0,
                     'ignoreallowance'  => 0,
                     'mark'             => 0]
                );
            }

            // we do groceries ever two days:
            for ($i = 1; $i <= $current->format('t'); $i++) {
                $d = sprintf('%02d', $i);
                if ($i % 2 == 0) {
                    $shopping = Transaction::create(
                        ['user_id'          => $user->id,
                         'account_id'       => $mainAccount->id,
                         'description'      => 'Daily groceries',
                         'amount'           => rand(-25,-11),
                         'date'             => $current->format('Y-m-') . $d,
                         'ignoreprediction' => 0,
                         'ignoreallowance'  => 0,
                         'mark'             => 0]
                    );
                }
            }


            $count++;
//            for ($i = 0; $i < 3; $i++) {
//                $account = Account::orderBy(DB::Raw('RAND()'))->first();
//                $amount = rand(-1, -200);
//                $day = rand(10, 28);
//                if($i == 0) {
//                    $day = 10; // need at least one transaction on the 10th for predictions.
//                    // and testing.
//                }
//                $setObjects = rand(1,4);
//
//                $t = Transaction::create(
//                    ['user_id'          => $user->id,
//                     'account_id'       => $account->id,
//                     'description'      => 'Test Transaction #' . $count,
//                     'amount'           => $amount,
//                     'date'             => $current->format('Y-m-').$day,
//                     'ignoreprediction' => 0, 'ignoreallowance' => 0,
//                     'mark'             => 0]
//                );
//                switch($setObjects) {
//                    case 1:
//                        // set beneficiary
//                        $beneficiary = Component::orderBy(DB::Raw('RAND()'))->first();
//                        $t->components()->save($beneficiary);
//
//                        break;
//                    case 2:
//                        // set a budget and a category
//                        $budget = Component::orderBy(DB::Raw('RAND()'))->first();
//                        $category = Component::orderBy(DB::Raw('RAND()'))->first();
//                        $t->components()->save($budget);
//                        $t->components()->save($category);
//                        break;
//                    case 3:
//                        // set all.
//                        $budget = Component::orderBy(DB::Raw('RAND()'))->first();
//                        $category = Component::orderBy(DB::Raw('RAND()'))->first();
//                        $beneficiary = Component::orderBy(DB::Raw('RAND()'))->first();
//                        $t->components()->save($beneficiary);
//                        $t->components()->save($budget);
//                        $t->components()->save($category);
//                        break;
//                    case 4:
//                        // set none.
//                        break;
//                }
//
//
//            }

            $current->addMonth();
        }
    }
} 