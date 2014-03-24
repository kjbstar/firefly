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

        // set date:
        $date = new \Carbon\Carbon();
        $date->startOfMonth();
        // create setting:
        Setting::create(
            [
                'user_id' => Auth::user()->id,
                'type'    => 'int',
                'name'    => 'specificAllowance',
                'date'    => $date->format('Y-m-d'),
                'value'   => 2000
            ]
        );

        // create expense on shared account:
        $date->addDay();
        $account = Auth::user()->accounts()->shared()->first();
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

        // add budget to expense:
        // find budget 'bills':
        $budgets = Auth::user()->components()->where('type','budget')->get();
        foreach($budgets as $budget) {
            if($budget->name == 'Bills') {
                $expense->components()->save($budget);
                $expense->save();
                break;
            }
        }

    }

    /**
     * Money that's spent on a shared account
     * does not count against your allowance
     *
     */
    public function testExpenseIgnoredByAllowance()
    {
        $date = new Carbon\Carbon();
        $date->startOfMonth();

        // get allowance:
        $allowance = HomeHelper::getAllowance($date);
        // skip two days
        $date->addDays(2);
        // get allowance again
        $newAllowance = HomeHelper::getAllowance($date);

        // should make no difference.
        $this->assertEquals($allowance['spent'],$newAllowance['spent']);
        $this->assertEquals($allowance['pct'],$newAllowance['pct']);
    }

    /**
     * Budget 'bills' should not be influenced by this transaction:
     */
    public function testExpenseIgnoredByBudget() {
        $date = new Carbon\Carbon();
        $date->startOfMonth();
        $budgets = HomeHelper::budgetOverview($date);
        $shouldAppear = false;
        $value = null;
        foreach($budgets as $id => $budget) {
            if($budget['name'] == 'Bills') {
                $shouldAppear = true;
            }
        }

        // now recheck:
        $date->addDays(3);
        $newBudgets = HomeHelper::budgetOverview($date);

        if($shouldAppear) {
            // budget should be in newBudgets, but with the
            // same amount
            $this->assertTrue(false,'No test yet!');
            //$this->assertEquals($budget['spent'],$budgets[$id]['spent']);
        } else {
            // budget should not be in newBudgets.
            foreach($newBudgets as $id => $budget) {
                if($budget['name'] == 'Bills') {
                    $this->assertTrue(false,'Budget should not be in this array!');
                }
            }
        }
    }

    /**
     * Transfers TO this account should have a budget.
     */

    public function tearDown() {
        parent::tearDown();
        DB::table('settings')->where('name', 'specificAllowance')->delete();
        $account = DB::table('accounts')->where('shared',1)->first();
        DB::table('transactions')->where('account_id',$account->id)->delete();
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();


    }

} 