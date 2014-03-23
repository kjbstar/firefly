<?php

class AccountHelperTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $user = User::where('username', 'test')->first();
        $this->be($user);
    }

    public function testAccountsAsSelectList()
    {
        // TODO implement
    }

    public function testGenerateTransactionListByMonth()
    {
        // TODO implement
    }

    public function testGenerateOverviewOfMonths()
    {
        // TODO implement
    }

    public function testGetPredictionStart()
    {
        // TODO implement
    }

    public function testGetMarkedTransactions()
    {
        // TODO implement
    }

} 