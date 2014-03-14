<?php

class TestSeedTransfers extends Seeder {
    public function run()
    {
        DB::table('transfers')->delete();
        $user = User::first();

        $today = new Carbon\Carbon;
        $twoYears = clone $today;
        $twoYears->subYears(2);
        $current = clone $twoYears;
        $count = 0;
        while ($current < $today) {
            $count++;
            for ($i = 0; $i < 2; $i++) {
                $accountFrom = Account::orderBy(DB::Raw('RAND()'))->first();
                $accountTo = Account::where('id','!=',$accountFrom->id)->orderBy(DB::Raw('RAND()'))->first();
                $amount = rand(1, 20);
                $day = rand(10, 28);

                Transfer::create(
                    ['user_id'          => $user->id,
                     'accountfrom_id'       => $accountFrom->id,
                     'accountto_id'       => $accountTo->id,
                     'description'      => 'Test Transfer #' . $count,
                     'amount'           => $amount,
                     'date'             => $current->format('Y-m-').$day,

                     ]
                );

            }

            $current->addMonth();
        }
    }
} 