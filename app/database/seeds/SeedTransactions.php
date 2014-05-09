<?php


class SeedTransactions extends Seeder {
    // create a transaction
    public function run()
    {
        Eloquent::unguard();
        $user = User::whereUsername('admin')->first();
        $account = Account::first();

        Transaction::create(
            [
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'user_id' => $user->id,
                'account_id'=> $account->id,
                'predictable_id' => null,
                'description' => 'Test transaction',
                'amount' => 1000,
                'date' => '2014-01-02',
                'ignoreprediction' => 0,
                'ignoreallowance' => 0,
                'mark' => 0,
            ]
        );


    }
}