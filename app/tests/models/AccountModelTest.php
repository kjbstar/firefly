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
        $raw = DB::table('balancemodifiers')->where('account_id', $account->id)->count();
        $this->assertEquals($raw, $db);
    }

    public function testPredictOnDateExpanded()
    {
        $account = Auth::user()->accounts()->first();
        $date = Carbon\Carbon::create(2014, 03, 1);
        $result = $account->predictOnDateExpanded($date);

        // I have no way of seeing if the predicted result
        // is accurate (randomness in the test seeds)
        // but at least we can check the content of the result array.

        $this->assertCount(3,$result);
        $this->assertLessThanOrEqual($result['prediction']['most'],$result['prediction']['prediction']);
        $this->assertLessThanOrEqual($result['prediction']['prediction'],$result['prediction']['least']);
    }

    public function testPredictOnDateExpandedSmallPredictable()
    {
        // TODO write tests.
        $account = Auth::user()->accounts()->first();
        $date = Carbon\Carbon::create(2014, 03, 3);
        $result = $account->predictOnDateExpanded($date);

        // I have no way of seeing if the predicted result
        // is accurate (randomness in the test seeds)
        // but at least we can check the content of the result array.

        $this->assertCount(3,$result);
        $this->assertLessThanOrEqual($result['prediction']['most'],$result['prediction']['prediction']);
        $this->assertLessThanOrEqual($result['prediction']['prediction'],$result['prediction']['least']);
    }

    public function testPredictOnDateExpandedAveragePredictable()
    {
        // TODO write tests.
        $account = Auth::user()->accounts()->first();
        $date = Carbon\Carbon::create(2014, 03, 4);
        $result = $account->predictOnDateExpanded($date);

        // I have no way of seeing if the predicted result
        // is accurate (randomness in the test seeds)
        // but at least we can check the content of the result array.

        $this->assertCount(3,$result);
        $this->assertLessThanOrEqual($result['prediction']['most'],$result['prediction']['prediction']);
        $this->assertLessThanOrEqual($result['prediction']['prediction'],$result['prediction']['least']);
    }



    public function testPredictOnDate()
    {
        // simply to hit the code
        // TODO write tests.


        $account = Auth::user()->accounts()->first();
        $date = Carbon\Carbon::create(2014, 03, 2);
        $result = $account->predictOnDate($date);

        // I have no way of seeing if the predicted result
        // is accurate (randomness in the test seeds)
        // but at least we can check the content of the result array.

        $this->assertCount(3,$result);
        $this->assertLessThanOrEqual($result['most'],$result['prediction']);
        $this->assertLessThanOrEqual($result['prediction'],$result['least']);
    }

    public function testTransactions()
    {
        $account = Auth::user()->accounts()->first();
        $raw = DB::table('transactions')->where('account_id', $account->id)->count();
        $count = $account->transactions()->count();
        $this->assertEquals($raw, $count);
    }

    public function testGetNullNameAttribute()
    {
        $account = Auth::user()->accounts()->first();
        $account->name = null;
        $this->assertNull($account->name);
    }

    public function testGetNameAttribute()
    {
        $account = Auth::user()->accounts()->first();
        $account->name = 'Bla bla bla';
        $this->assertNotNull($account->name);
        $this->assertEquals('Bla bla bla', $account->name);
    }

    public function setNameAttribute() {
        // TODO also save it?
    }

    public function testGetDates()
    {
        $account = Auth::user()->accounts()->first();
        $this->assertInstanceOf('\Carbon\Carbon', $account->created_at);
        $this->assertInstanceOf('\Carbon\Carbon', $account->updated_at);
        $this->assertInstanceOf('\Carbon\Carbon', $account->openingbalancedate);


    }

    public function testScopeNotHidden()
    {
        $raw = Auth::user()->accounts()->where('hidden',0)->count();
        $count = Auth::user()->accounts()->notHidden()->count();
        $this->assertEquals($raw,$count);
    }

    public function testScopeShared()
    {
        $raw = Auth::user()->accounts()->where('shared',1)->count();
        $count = Auth::user()->accounts()->shared()->count();
        $this->assertEquals($raw,$count);
    }
} 