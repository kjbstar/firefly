<?php


class SeedPiggybanks extends Seeder
{
    public function run()
    {
        Eloquent::unguard();
        $user = User::whereUsername('admin')->first();

        Piggybank::create(
            [
                'user_id' => $user->id,
                'name' => 'New television',
                'amount' => 0,
                'target' => 1000,
                'order' => 1
            ]
        );
    }
} 