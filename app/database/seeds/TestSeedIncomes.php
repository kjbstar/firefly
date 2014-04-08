<?php

class TestSeedIncomes extends Seeder
{
    public function run()
    {
        $user = User::first();

        $today = new Carbon\Carbon;
        $today->subMonth();
        $twoYears = clone $today;
        $twoYears->subYears(2);
        $current = clone $twoYears;
        $count = 0;
        while ($current < $today) {
            $count++;
            $account = Account::orderBy(DB::Raw('RAND()'))->first();
            $amount = rand(2500, 3000);
            $day = rand(24, 26);

            Transaction::create(
                ['user_id'          => $user->id, 'account_id' => $account->id,
                 'description'      => 'Salary',
                 'amount'           => $amount,
                 'date'             => $current->format('Y-m-') . $day,
                 'ignoreprediction' => 0, 'ignoreallowance' => 0, 'mark' => 1]
            );


            $current->addMonth();
        }
    }

} 