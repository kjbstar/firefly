<?php
/**
 * Generated by PHPUnit_SkeletonGenerator on 2014-05-09 at 17:06:51.
 */
class SearchControllerTest extends TestCase
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
     * @covers SearchController::search
     * @todo   Implement testSearch().
     */
    public function testSearch()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
