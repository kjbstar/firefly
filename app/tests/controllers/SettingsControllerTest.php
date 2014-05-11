<?php
/**
 * Generated by PHPUnit_SkeletonGenerator on 2014-05-09 at 16:59:06.
 */
class SettingsControllerTest extends TestCase
{

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();
        $user = User::where('username', 'admin')->first();
        $this->be($user);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers SettingsController::index
     */
    public function testIndex()
    {
        foreach(Setting::get() as $s) {
            $s->delete();
        }
        $count = Setting::count();
        $response = $this->action('GET', 'SettingsController@index');
        $view = $response->original;
        $newCount = Setting::count();

        $this->assertResponseOk();
        $this->assertEquals('Settings',$view['title']);
        $this->assertSessionHas('previous');
        $this->assertEquals($count+2,$newCount);
    }

    /**
     * @covers SettingsController::postIndex
     * @todo   Implement testPostIndex().
     */
    public function testPostIndex()
    {
        // settings should now exist (and be empty / invalid)
        // update both, see if it sticks.
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers SettingsController::allowances
     * @todo   Implement testAllowances().
     */
    public function testAllowances()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers SettingsController::postAllowances
     * @todo   Implement testPostAllowances().
     */
    public function testPostAllowances()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers SettingsController::addAllowance
     * @todo   Implement testAddAllowance().
     */
    public function testAddAllowance()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers SettingsController::postAddAllowance
     * @todo   Implement testPostAddAllowance().
     */
    public function testPostAddAllowance()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers SettingsController::editAllowance
     * @todo   Implement testEditAllowance().
     */
    public function testEditAllowance()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers SettingsController::postEditAllowance
     * @todo   Implement testPostEditAllowance().
     */
    public function testPostEditAllowance()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers SettingsController::deleteAllowance
     * @todo   Implement testDeleteAllowance().
     */
    public function testDeleteAllowance()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers SettingsController::postDeleteAllowance
     * @todo   Implement testPostDeleteAllowance().
     */
    public function testPostDeleteAllowance()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
