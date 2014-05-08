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

        $this->assertEquals('All accounts',$view['title']);

        // test the count of the accounts.
        $count = DB::table('accounts')->where('user_id',$user->id)->count();
        $this->assertCount($count,$view['accounts']);


    }

    /**
     * @covers AccountController::add
     * @todo   implement
     */
    public function testAdd()
    {
        $user = User::whereUsername('admin')->first();
        $this->be($user);

        $response = $this->action('GET', 'AccountController@add');
        $view = $response->original;

        $this->assertResponseOk();
        $this->assertSessionHas('previous');

        $this->assertEquals('Add a new account',$view['title']);


    }

    /**
     * @covers AccountController::postAdd
     * @todo   implement
     */
    public function testPostAdd()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers AccountController::edit
     * @todo   implement
     */
    public function testEdit()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers AccountController::postEdit
     * @todo   implement
     */
    public function testPostEdit()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
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