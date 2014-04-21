<?php

class TestSeedPiggies extends Seeder
{
    public function run()
    {
        DB::table('piggybanks')->delete();
        $user = User::first();

        Piggybank::create(
            ['user_id' => $user->id, 'name' => 'Test Piggy #1',
             'amount' => 0,
             'target'  => 200,'order' => 1]
        );
        Piggybank::create(
            ['user_id' => $user->id, 'name' => 'Test Piggy #2',
             'amount' => 0,
             'target'  => 400,'order' => 2]
        );
        Piggybank::create(
            ['user_id' => $user->id, 'name' => 'Test Piggy #3',
             'amount' => 0,
             'target'  => null,'order' => 3]
        );
    }
} 