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
        $list = HomeHelper::homeAccountList(new \Carbon\Carbon());
        $db = Auth::user()->accounts()->notHidden()->get();
        $this->assertEquals(count($list), count($db));
        foreach ($db as $item) {
            $this->assertEquals($item->name, $item['name']);
        }
    }

    public function testBudgetOverview()
    {
        // TODO implement.
        HomeHelper::budgetOverview(new \Carbon\Carbon());
    }

    public function testGetAllowance()
    {
        // TODO implement
        HomeHelper::getAllowance(new \Carbon\Carbon());

    }

    public function testGetPredictables()
    {
        // TODO implement
        HomeHelper::getPredictables(new \Carbon\Carbon());
    }

}