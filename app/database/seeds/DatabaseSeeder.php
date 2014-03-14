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
        if (App::environment() == 'testing') {
            // create a user
            $this->call('TestSeedUser');

            // create three accounts
            $this->call('TestSeedAccounts');

            // create six components
            $this->call('TestSeedComponents');

            // give three of them limits
            $this->call('TestSeedLimits');

            // create 72 transactions, 3 expenses per month
            // over 2014 and 2013. (thisyear and prevyear)
            $this->call('TestSeedExpenses');

            // create 24 transactions, 1 income per month
            // over 2014 and 2013. (thisyear and prevyear)
            $this->call('TestSeedIncomes');

            // create 24 transfers, 2 per month.
            // (back and forth between 2 accounts)
            $this->call('TestSeedTransfers');

            // create two piggy banks
            $this->call('TestSeedPiggies');

            // create two predictables.
            $this->call('TestSeedPredictables');



        }
    }

}