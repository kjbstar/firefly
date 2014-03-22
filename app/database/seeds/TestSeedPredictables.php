<?php

class TestSeedPredictables extends Seeder
{
    public function run()
    {
        DB::table('predictables')->delete();
        $user = User::first();

        //simply create two predictables.
//        $p = Predictable::create(
//            ['user_id' => $user->id, 'description' => 'TestPredictable #1',
//             'amount'  => -100, 'pct' => 90, 'dom' => 10, 'inactive' => 0]
//        );

//        // create transaction in each month that falls into this
//        // predictable:
//        $today = new Carbon\Carbon;
//        $twoYears = clone $today;
//        $twoYears->subYears(2);
//        $current = clone $twoYears;
//        while ($current < $today) {
//            $amount = rand(-190, -10);
//            $account = Account::orderBy(DB::Raw('RAND()'))->first();
//            Transaction::create(
//                ['user_id'          => $user->id, 'account_id' => $account->id,
//                 'description'      => 'TestPredictable #1',
//                 'amount'           => $amount,
//                 'predictable_id' => $p->id,
//                 'date'             => $current->format('Y-m-') .'10',
//                 'ignoreprediction' => 0, 'ignoreallowance' => 0, 'mark' => 0]
//            );
//            $current->addMonth();
//        }
//
//        // create one for any day, just in case.
//        Predictable::create(
//            ['user_id' => $user->id, 'description' => 'TestPredictable #2',
//             'amount'  => -150, 'pct' => 50, 'dom' => 3, 'inactive' => 0]
//        );
        // predictable for rent:
        Predictable::create(
            ['user_id' => $user->id, 'description' => 'Rent',
             'amount'  => -500, 'pct' => 5, 'dom' => 1, 'inactive' => 0]
        );

        // predictable for insurance:
        Predictable::create(
            ['user_id' => $user->id, 'description' => 'Insurance',
             'amount'  => -120, 'pct' => 5, 'dom' => 2, 'inactive' => 0]
        );


    }
}