<?php


class AccountModelTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $user = User::where('username', 'test')->first();
        $this->be($user);
    }

    public function testUser()
    {
        $account = Auth::user()->accounts()->first();
        $this->assertEquals(
            Auth::user()->id, $account->user()->first()->id
        );

    }

    public function testTransfersto()
    {
        $account = Auth::user()->accounts()->first();

        // count transfers to this account:
        $result = DB::table('transfers')->where('accountto_id', $account->id)
            ->count();

        // should match model:
        $this->assertEquals($account->transfersto()->count(), $result);
    }

    public function testTransfersfrom()
    {
        $account = Auth::user()->accounts()->first();

        // count transfers to this account:
        $result = DB::table('transfers')->where('accountfrom_id', $account->id)
            ->count();

        // should match model:
        $this->assertEquals($account->transfersfrom()->count(), $result);
    }

    public function testBalanceOnDate()
    {
        $date = new Carbon\Carbon;
        $account = Auth::user()->accounts()->first();
        $balance = floatval(
            DB::table('balancemodifiers')->where('account_id', $account->id)
                ->where(
                'date', '<=', $date->format('Y-m-d')
            )->sum('balance')
        );
        $this->assertEquals($balance, $account->balanceOnDate($date));
    }

    public function testBalanceOnDateBeforeOpening()
    {
        $account = Auth::user()->accounts()->first();
        $date = $account->openingbalancedate;


        $balance = floatval(
            DB::table('balancemodifiers')->where('account_id', $account->id)
                ->where(
                    'date', '<=', $date->format('Y-m-d')
                )->sum('balance')
        );
        $date->subDays(2);
        $this->assertEquals($balance, $account->balanceOnDate($date));
    }

    public function testBalancemodifiers()
    {
        $account = Auth::user()->accounts()->first();
        $db = $account->balancemodifiers()->count();
        $raw = DB::table('balancemodifiers')->where('account_id',$account->id)->count();
        $this->assertEquals($raw,$db);
    }

    public function testPredictOnDateExpanded()
    {
        $account = Auth::user()->accounts()->first();
        $date = new Carbon\Carbon;
        $result = $account->predictOnDateExpanded($date);
    }

    public function testPredictOnDate()
    {
    }

    public function testTransactions()
    {
    }

    public function testGetNameAttribute()
    {
    }

    public function testSetNameAttribute()
    {
    }

    public function testGetDates()
    {
    }

    public function testScopeNotHidden()
    {
    }
} 