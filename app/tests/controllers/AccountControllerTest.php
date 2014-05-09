<?php

use Carbon\Carbon as Carbon;


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
     * @covers AccountController::postEdit
     */
    public function testPostEditFailsValidator()
    {
        // find account to edit.
        $account = DB::table('accounts')->first();

        $newData = [
            'name' => null,
        ];

        // this should update the account.
        $this->call('POST', '/home/account/' . $account->id . '/edit/', $newData);

        $this->assertSessionHas('error');
        $this->assertResponseStatus(302);
        $newAccount = DB::table('accounts')->find($account->id);

        // account name should match old name.
        $this->assertEquals($account->name, $newAccount->name);
    }

    /**
     * @covers AccountController::postEdit
     */
    public function testPostEditFailsTrigger()
    {
        // find account to edit.
        $account = DB::table('accounts')->first();

        // find another account
        $otherAccount = DB::table('accounts')->where('id', '!=', $account->id)->first();

        // valid data, but account name is already in use:
        $newData = [
            'name'               => $otherAccount->name,
            'openingbalance'     => $account->openingbalance,
            'openingbalancedate' => $account->openingbalancedate,
            'inactive'           => $account->inactive,
            'shared'             => $account->shared
        ];

        // this should (try to) update the account.
        $this->call('POST', '/home/account/' . $account->id . '/edit/', $newData);

        $this->assertSessionHas('error');
        $this->assertResponseStatus(302);
        $newAccount = DB::table('accounts')->find($account->id);

        // account name should match old name.
        $this->assertEquals($account->name, $newAccount->name);
    }

    /**
     * @covers AccountController::delete
     */
    public function testDelete()
    {
        // find an account to delete:
        $account = DB::table('accounts')->first();
        $response = $this->action('GET', 'AccountController@delete', $account->id);
        $view = $response->original;

        $this->assertResponseOk();
        $this->assertSessionHas('previous');
        $this->assertEquals('Delete account "' . $account->name . '"', $view['title']);
        $this->assertEquals($account->name, $view['account']->name);
    }

    /**
     * @covers AccountController::postDelete
     */
    public function testPostDelete()
    {
        // create account, delete it.
        $user = User::whereUsername('admin')->first();
        $toDelete = Account::create(
            [
                'user_id'            => $user->id,
                'name'               => 'To be deleted.',
                'openingbalance'     => 1000,
                'openingbalancedate' => '2014-01-01',
                'currentbalance'     => 1000,
                'inactive'           => 0,
                'shared'             => 0
            ]
        );
        $count = Account::count();

        // this should delete the account.
        $this->call('POST', '/home/account/' . $toDelete->id . '/delete/');
        $newCount = Account::count();

        $this->assertSessionHas('success');
        $this->assertResponseStatus(302);
        $this->assertEquals($count - 1, $newCount);
    }

    /**
     * @covers AccountController::overview
     */
    public function testOverview()
    {
        // get an account:
        $account = Account::first();

        // this should get the overview
        $response = $this->call('GET', '/home/account/' . $account->id . '/overview/');
        $view = $response->original;

        // count for $months array should match
        $start = $account->openingbalancedate;
        $start->firstOfMonth();
        $diff = $start->diffInMonths(new Carbon) + 1;
        $this->assertCount($diff,$view['months']);

        $this->assertResponseOk();
        $this->assertEquals('Overview for account "' . $account->name . '"', $view['title']);


    }

    /**
     * @covers AccountController::overviewChart
     */
    public function testOverviewChart()
    {
        // get an account:
        $account = Account::first();

        // this should get the overview
        $response = $this->call('GET', '/home/account/' . $account->id . '/overview/chart');
        $jsonContent = $response->getContent();
        $json = json_decode($jsonContent);

        // count for $months array should match
        $start = $account->openingbalancedate;
        $start->firstOfMonth();
        $diff = $start->diffInMonths(new Carbon) + 1;

        $this->assertResponseOk();

        // there should be two columns
        $this->assertCount(2,$json->cols);

        // there should be $diff rows:
        $this->assertCount($diff,$json->rows);

    }

    /**
     * @covers AccountController::overviewByMonth
     */
    public function testOverviewByMonth()
    {

        // get an account:
        $account = Account::first();

        // get a date
        $date = new Carbon;

        // this should get the overview
        $response = $this->call('GET', '/home/account/' . $account->id . '/overview/' . $date->format('Y/m'));
        $view = $response->original;

        // count for $mutations array should match
        $transfersFrom = Transfer::whereAccountfromId($account->id)->where(DB::Raw('DATE_FORMAT(`date`,"%Y-%m")'),$date->format('Y-m'))->count();
        $transfersTo = Transfer::whereAccounttoId($account->id)->where(DB::Raw('DATE_FORMAT(`date`,"%Y-%m")'),$date->format('Y-m'))->count();
        $transactions = Transaction::whereAccountId($account->id)->where(DB::Raw('DATE_FORMAT(`date`,"%Y-%m")'),$date->format('Y-m'))->count();
        $sum = $transfersFrom + $transfersTo + $transactions;
        $this->assertCount($sum,$view['mutations']);

        $this->assertResponseOk();
        $this->assertEquals('Overview for account "' . $account->name . '" in '.$date->format('F Y'), $view['title']);
    }

    /**
     * @covers AccountController::overviewChartByMonth
     */
    public function testOverviewChartByMonth()
    {
        // get an account:
        $account = Account::first();

        // get a date
        $date = new Carbon;

        // this should get the overview
        $response = $this->call('GET', '/home/account/' . $account->id . '/overview/chart/' . $date->format('Y/m'));
        $jsonContent = $response->getContent();
        $json = json_decode($jsonContent);

        $this->assertResponseOk();

        // there should be seven columns
        $this->assertCount(7,$json->cols);

        // there should be as many rows as days in this month:
        $this->assertCount(intval($date->format('t')),$json->rows);


    }

    /**
     * @covers AccountController::predict
     */
    public function testPredict()
    {
        // get an account:
        $account = Account::first();

        // get a date
        $date = new Carbon;

        // this should get the overview
        $response = $this->call('GET', '/home/account/' . $account->id . '/predict/' . $date->format('Y/m/d'));
        $view = $response->original;

        $this->assertResponseOk();

        $this->assertEquals($date->format('Ymd'),$view['date']->format('Ymd'));
    }
} 