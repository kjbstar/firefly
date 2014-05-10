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
            $this->call('NewUserSeeder');
            $this->call('SeedAccounts');
            $this->call('SeedComponents');
            $this->call('SeedTransactions');
            $this->call('SeedTransfers');
            $this->call('SeedPiggybanks');
            $this->call('SeedPredictables');
        } else {
            // seed anything else
        }


    }

}