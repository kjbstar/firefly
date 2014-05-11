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

        // transfer for Albert Heijn, Food and drinks and Daily groceries
        // ben cat bud

        $transaction = Transaction::create(
            [
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'user_id' => $user->id,
                'account_id'=> $account->id,
                'predictable_id' => null,
                'description' => 'Test transaction',
                'amount' => 1000,
                'date' => '2014-01-03',
                'ignoreprediction' => 0,
                'ignoreallowance' => 0,
                'mark' => 0,
            ]
        );
        $ah = Component::where('name','Albert Heijn')->first();
        $fd = Component::where('name','Food and drinks')->first();
        $dg = Component::where('name','Daily groceries')->first();
        $transaction->components()->save($ah);
        $transaction->components()->save($fd);
        $transaction->components()->save($dg);

    }
}