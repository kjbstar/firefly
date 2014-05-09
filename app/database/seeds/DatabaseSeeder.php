<?php

class DatabaseSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();
        $env = App::environment();
        if ($env == 'local') {
            exit('LOCAL');
        }

        // create the types
        $this->call('SeedTypes');


        if ($env == 'testing' || $env == 'travis') {
            // seed test data.

            // seed new user
            $this->call('NewUserSeeder');

            // seed accounts:
            $this->call('SeedAccounts');

            // seed components:
            $this->call('SeedComponents');
        } else {
            // seed anything else
        }


    }

}