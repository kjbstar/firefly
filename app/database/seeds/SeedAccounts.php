<?php


class SeedAccounts extends Seeder
{
    public function run()
    {
        Eloquent::unguard();
        $user = User::whereUsername('admin')->first();
        // create two accounts:
        $checking = Account::create(
            [
                'user_id'            => $user->id,
                'name'               => 'Checking account',
                'openingbalance'     => 1000,
                'openingbalancedate' => '2013-01-01',
                'currentbalance'     => 1000,
                'inactive'           => 0,
                'shared'             => 0
            ]
        );

        $saving = Account::create(
            [
                'user_id'            => $user->id,
                'name'               => 'Savings account',
                'openingbalance'     => 10000,
                'openingbalancedate' => '2013-01-01',
                'currentbalance'     => 10000,
                'inactive'           => 0,
                'shared'             => 0
            ]
        );


    }

}
