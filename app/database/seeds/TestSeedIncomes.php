<?php

class TestSeedIncomes extends Seeder
{
    public function run()
    {
        $user = User::first();

        $today = new Carbon\Carbon;
        $twoYears = clone $today;
        $twoYears->subYears(2);
        $current = clone $twoYears;
        $count = 0;
        while ($current < $today) {
            $count++;
            $account = Account::orderBy(DB::Raw('RAND()'))->first();
            $amount = rand(1500, 2500);
            $day = rand(10, 28);

            Transaction::create(
                ['user_id'          => $user->id, 'account_id' => $account->id,
                 'description'      => 'Test Transaction (income) #' . $count,
                 'amount'           => $amount,
                 'date'             => $current->format('Y-m-') . $day,
                 'ignoreprediction' => 0, 'ignoreallowance' => 0, 'mark' => 0]
            );


            $current->addMonth();
        }
    }

} 