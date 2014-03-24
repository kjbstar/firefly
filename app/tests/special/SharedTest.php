<?php

/**
 * Class SharedTest
 *
 * These test cases work on the new "shared" account feature.
 */
class SharedTest extends TestCase
{


    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
    }

    public function setUp()
    {
        parent::setUp();
        $user = User::where('username', 'test')->first();
        $this->be($user);
    }

    /**
     * Money that's spent on a shared account
     * does not count against your allowance
     *
     */
    public function testExpenseIgnoredByAllowance()
    {
        $date = new Carbon\Carbon();
        $account = Auth::user()->accounts()->shared()->first();
        // set a new allowance for this $date
        Setting::create(
            [
                'user_id' => Auth::user()->id,
                'type'    => 'int',
                'name'    => 'specificAllowance',
                'date'    => $date->format('Y-m-') . '01',
                'value'   => 2000
            ]
        );
        $allowance = HomeHelper::getAllowance($date);

        // spend money on the shared account:

        $expense = new Transaction(
            [
                'user_id'          => Auth::user()->id,
                'account_id'       => $account->id,
                'description'      => 'Huur ofzo',
                'amount'           => -500,
                'date'             => $date->format('Y-m-d'),
                'ignoreprediction' => 0,
                'ignoreallowance'  => 0,
                'mark'             => 0,
            ]
        );
        $expense->account()->associate($account);
        $expense->save();

        $newAllowance = HomeHelper::getAllowance($date);
        $this->assertEquals($allowance['spent'],$newAllowance['spent']);
        $this->assertEquals($allowance['pct'],$newAllowance['pct']);


    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        DB::table('settings')->where('name', 'specificAllowance')->delete();
        $account = DB::table('accounts')->where('shared',1)->first();
        DB::table('transactions')->where('account_id',$account->id)->delete();

    }

} 