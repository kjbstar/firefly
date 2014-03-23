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
        $list = AccountHelper::accountsAsSelectList();
        $db = Auth::user()->accounts()->get();
        $this->assertEquals(count($list), count($db));
        foreach ($db as $item) {
            $this->assertArrayHasKey($item->id, $list);
            $this->assertEquals($item->name, $list[$item->id]);
        }
    }

    public function testGenerateTransactionListByMonth()
    {
        $date = new \Carbon\Carbon();
        $date->subYear();
        $account = Auth::user()->accounts()->first();
        $count = $account->transactions()->orderBy('date', 'DESC')->inMonth($date)->count();

        $list = AccountHelper::generateTransactionListByMonth($account, $date);
        $this->assertCount($count, $list);
    }

    public function testGenerateOverviewOfMonths()
    {
        // we know there are accounts:
        $account = Auth::user()->accounts()->orderBy('openingbalancedate', 'ASC')->first();
        $start = $account->openingbalancedate;
        $end = new \Carbon\Carbon();
        $diff = $start->diffInMonths($end) + 1;
        $list = AccountHelper::generateOverviewOfMonths($account);
        $this->assertCount($diff, $list);
    }

    public function testGetPredictionStart()
    {
        // at this point, we have no settings.
        // ergo, it should be the default one.
        $setting = AccountHelper::getPredictionStart();
        $config = new \Carbon\Carbon(Config::get('firefly.predictionStart.value'));
        $this->assertInstanceOf('\Carbon\Carbon', $setting);
        $this->assertEquals($config, $setting);
    }

    public function testGetMarkedTransactions()
    {

        $start = new \Carbon\Carbon();
        $end = clone $start;
        $start->subYear();
        $account = Auth::user()->accounts()->first();

        // count ourselves:
        $count = DB::table('transactions')->where('mark', 1)->where('date', '>=', $start->format('Y-m-d'))
            ->where('date', '<=', $end->format('Y-m-d'))->where('account_id', '=', $account->id)->count();
        $marked = AccountHelper::getMarkedTransactions($account, $start, $end);

        $this->assertCount($count, $marked);

    }

} 