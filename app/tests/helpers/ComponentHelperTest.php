<?php


class ComponentHelperTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $user = User::where('username', 'test')->first();
        $this->be($user);
    }

    public function testGenerateOverviewOfMonths()
    {
        // TODO implement
    }

    public function testGenerateTransactionListByMonth()
    {
        // TODO implement
    }

    public function testTransactionsWithoutComponent()
    {
        // TODO implement
    }

    public function testHasComponent()
    {
        // TODO implement
    }

    public function testGetParentList()
    {
        // TODO implement
    }

} 