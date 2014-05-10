<?php

class SeedPredictables extends Seeder
{
    public function run()
    {
        Eloquent::unguard();
        $user = User::whereUsername('admin')->first();
        $account = $user->accounts()->first();

        Predictable::create(
            [
                'user_id'     => $user->id,
                'account_id'  => $account->id,
                'description' => 'Rent',
                'amount'      => 500,
                'dom'         => 1,
                'pct'         => 1,
                'inactive'    => 0
            ]
        );

    }
}