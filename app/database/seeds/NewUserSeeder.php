<?php


class NewUserSeeder extends Seeder
{
    //
    public function run()
    {
        Eloquent::unguard();
        $user = User::whereUsername('admin')->first();
        if (is_null($user)) {
            // create it:
            User::create(
                [
                    'username'       => 'admin',
                    'origin'         => '',
                    'email'          => 'admin@local',
                    'password'       => Hash::make('supersecret'),
                    'activation'     => null,
                    'remember_token' => null,
                    'reset'          => null
                ]
            );
        }


    }
}