<?php

class TestSeedUser extends Seeder
{
    public function run()
    {
        DB::table('users')->delete();
        $user = User::create(
            ['email'      => 'test@nder.be', 'username' => 'test',
             'password'   => Hash::make('test'), 'origin' => 'Test',
             'activation' => null, 'reset' => null]
        );
    }

} 