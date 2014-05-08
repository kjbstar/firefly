<?php


class AccountControllerTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();
        $user = User::whereUsername('admin')->first();
        $this->be($user);
    }

    public function testIfTrue()
    {

        $this->assertTrue(true);
    }

    /**
     * @covers AccountController::index
     * @todo   implement
     */
    public function testIndex()
    {
        $user = User::whereUsername('admin')->first();
        $this->be($user);

        $response = $this->action('GET', 'AccountController@index');
        $view = $response->original;

        $this->assertResponseOk();

        $this->assertEquals('All accounts', $view['title']);

        // test the count of the accounts.
        $count = DB::table('accounts')->where('user_id', $user->id)->count();
        $this->assertCount($count, $view['accounts']);


    }

    /**
     * @covers AccountController::add
     */
    public function testAdd()
    {
        $user = User::whereUsername('admin')->first();
        $this->be($user);

        $response = $this->action('GET', 'AccountController@add');
        $view = $response->original;

        $this->assertResponseOk();
        $this->assertSessionHas('previous');

        $this->assertEquals('Add a new account', $view['title']);
    }

    /**
     * @covers AccountController::add
     */
    public function testAddWithOldInput()
    {
        $user = User::whereUsername('admin')->first();
        $this->be($user);

        $oldData = [
            'name'               => 'Old input (new account)',
            'openingbalance'     => '100',
            'openingbalancedate' => '2014-01-01'
        ];
        $this->session(['_old_input' => $oldData]);

        $response = $this->action('GET', 'AccountController@add');
        $view = $response->original;

        $this->assertResponseOk();

        $this->assertEquals('Add a new account', $view['title']);

        $this->assertEquals($oldData['name'], $view['prefilled']['name']);
        $this->assertEquals($oldData['openingbalancedate'], $view['prefilled']['openingbalancedate']);
    }

    /**
     * @covers AccountController::postAdd
     */
    public function testPostAdd()
    {
        $newData = [
            'name'               => 'New Account #' . rand(1000, 9999),
            'openingbalance'     => 1000,
            'openingbalancedate' => '2014-01-01',
            'inactive'           => 0,
        ];
        $count = Account::count();

        // this should create a new account.
        $this->action('POST', 'AccountController@postAdd', $newData);

        $newCount = Account::count();

        $this->assertSessionHas('success');
        $this->assertResponseStatus(302);
        $this->assertEquals($count + 1, $newCount);

        // delete the account again.
        Account::where('name', $newData['name'])->delete();

        $this->assertEquals($count, $newCount - 1);
    }

    /**
     * @covers AccountController::postAdd
     */
    public function testPostAddFailsValidator()
    {
        $newData = [
            'name'               => null,
            'openingbalance'     => 1000,
            'openingbalancedate' => '2014-01-01',
            'inactive'           => 0,
        ];
        $count = Account::count();

        // this should create a new account.
        $this->action('POST', 'AccountController@postAdd', $newData);

        $newCount = Account::count();

        $this->assertSessionHas('error');
        $this->assertResponseStatus(302);
        $this->assertEquals($count, $newCount);
    }

    /**
     * @covers AccountController::edit
     */
    public function testEdit()
    {
        // find an account to edit:
        $account = DB::table('accounts')->first();
        $response = $this->action('GET', 'AccountController@edit', $account->id);
        $view = $response->original;

        $this->assertResponseOk();
        $this->assertSessionHas('previous');
        $this->assertEquals('Edit account "' . $account->name . '"', $view['title']);
        $this->assertEquals($account->name, $view['account']->name);
        $this->assertEquals($account->name, $view['prefilled']['name']);

    }

    /**
     * @covers AccountController::edit
     */
    public function testEditWithOldInput()
    {
        // find an account to edit:
        $account = DB::table('accounts')->first();

        $oldData = [
            'name'               => 'Old input (edited account)',
            'openingbalance'     => '100',
            'openingbalancedate' => '2014-01-01'
        ];
        $this->session(['_old_input' => $oldData]);

        $response = $this->action('GET', 'AccountController@edit', $account->id);
        $view = $response->original;

        $this->assertResponseOk();
        $this->assertEquals('Edit account "' . $account->name . '"', $view['title']);
        $this->assertEquals($account->name, $view['account']->name);
        $this->assertEquals($oldData['name'], $view['prefilled']['name']);

    }

    /**
     * @covers AccountController::postEdit
     */
    public function testPostEdit()
    {
        // find account to edit.
        $account = DB::table('accounts')->first();
        $originalName = $account->name;

        $newData = [
            'name'               => 'New Account Name #' . rand(1000, 9999),
            'openingbalance'     => $account->openingbalance,
            'openingbalancedate' => $account->openingbalancedate,
            'inactive'           => $account->inactive,
            'shared'             => $account->shared
        ];

        // this should update the account.
        $this->call('POST', '/home/account/' . $account->id . '/edit/', $newData);

        $this->assertSessionHas('success');
        $this->assertResponseStatus(302);
        $newAccount = DB::table('accounts')->find($account->id);

        // new account name should match
        $this->assertEquals($newData['name'], $newAccount->name);

        // restore account again:
        DB::table('accounts')->whereId($account->id)->update(['name' => $originalName]);
    }

    /**
     * @covers AccountController::delete
     * @todo   implement
     */
    public function testDelete()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers AccountController::postDelete
     * @todo   implement
     */
    public function testPostDelete()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers AccountController::overview
     * @todo   implement
     */
    public function testOverview()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers AccountController::overviewChart
     * @todo   implement
     */
    public function testOverviewChart()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers AccountController::overviewByMonth
     * @todo   implement
     */
    public function testOverviewByMonth()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers AccountController::overviewChartByMonth
     * @todo   implement
     */
    public function testOverviewChartByMonth()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers AccountController::predict
     * @todo   implement
     */
    public function testPredict()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }
} 