<?php


class BalancemodifierModelTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $user = User::where('username', 'test')->first();
        $this->be($user);
    }

    public function testAccount()
    {
        $bm = Balancemodifier::first();
        $account = $bm->account()->first();
        $this->assertEquals($account->id,$bm->account_id);
    }

    public function testGetDates()
    {
        $bm = Auth::user()->accounts()->first()->balancemodifiers()->first();
        $this->assertInstanceOf('\Carbon\Carbon', $bm->created_at);
        $this->assertInstanceOf('\Carbon\Carbon', $bm->updated_at);
        $this->assertInstanceOf('\Carbon\Carbon', $bm->date);
    }

    public function testScopeOnDay()
    {
        $date = new Carbon\Carbon('2012-03-03');
        $raw = DB::table('balancemodifiers')->where('date','=',$date->format('Y-m-d'))->count();
        $count = Balancemodifier::onDay($date)->count();
        $this->assertEquals($raw,$count);
    }

    public function testScopeBeforeDay()
    {
        $date = new Carbon\Carbon('2012-03-03');
        $raw = DB::table('balancemodifiers')->where('date','<',$date->format('Y-m-d'))->count();
        $count = Balancemodifier::beforeDay($date)->count();
        $this->assertEquals($raw,$count);
    }


} 