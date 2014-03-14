<?php

class TestSeedPredictables extends Seeder
{
    public function run()
    {
        DB::table('predictables')->delete();
        $user = User::first();

        //simply create two predictables.
        Predictable::create([
                'user_id' => $user->id,
                'description' => 'TestPredictable #1',
                'amount' => -100,
                'pct' => 10,
                'dom' => 3,
                'inactive' => 0
            ]);

        Predictable::create([
                'user_id' => $user->id,
                'description' => 'TestPredictable #2',
                'amount' => -150,
                'pct' => 50,
                'dom' => 3,
                'inactive' => 0
            ]);

    }
}