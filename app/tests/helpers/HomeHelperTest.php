<?php


class HomeHelperTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $user = User::where('username', 'test')->first();
        $this->be($user);
    }

    public function testHomeAccountList()
    {
        // TODO implement
    }

    public function testBudgetOverview()
    {
        // TODO implement
    }

    public function testGetAllowance()
    {
        // TODO implement
    }

    public function testGetPredictables()
    {
        // TODO implement
    }

}