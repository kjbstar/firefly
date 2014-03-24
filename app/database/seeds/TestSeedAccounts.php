<?php

class TestSeedAccounts extends Seeder
{
    public function run()
    {
        DB::table('accounts')->delete();
        $user = User::first();
        $year = intval(date('Y'));
        for($i=0;$i<3;$i++) {
            $balance = rand(1000,3000);
            Account::create(
                ['user_id'            => $user->id, 'name' => 'TestAccount #' . ($i+1),
                 'openingbalance'     => $balance, 'openingbalancedate' => '2012-01-01',
                 'currentbalance'     => $balance, 'hidden' => 0]
            );

        }
        Account::create(
            [
                'user_id' => $user->id,
                'name' => 'Shared account',
                'openingbalance' => 1000,
                'currentbalance' => 1000,
                'openingbalancedate' => date('Y').'-01-01',
                'hidden' => 0,
                'shared' => 1
            ]
        );
    }

} 