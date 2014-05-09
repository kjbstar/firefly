<?php


class SeedTransfers extends Seeder {
    // create a transaction
    public function run()
    {
        Eloquent::unguard();
        $user = User::whereUsername('admin')->first();
        $account = Account::first();
        $other = Account::where('id','!=',$account->id)->first();

        Transfer::create(
            [
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'user_id' => $user->id,
                'accountfrom_id'=> $account->id,
                'accountto_id'=> $other->id,
                'description' => 'Test transfer #1',
                'amount' => 500,
                'date' => '2014-01-01',
                'ignoreallowance' => 0,
            ]
        );

        Transfer::create(
            [
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'user_id' => $user->id,
                'accountfrom_id'=> $other->id,
                'accountto_id'=> $account->id,
                'description' => 'Test transfer #2',
                'amount' => 500,
                'date' => '2014-01-01',
                'ignoreallowance' => 0,
            ]
        );


    }
}