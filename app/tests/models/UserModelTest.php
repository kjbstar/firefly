<?php


class UserModelTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $user = User::where('username', 'test')->first();
        $this->be($user);
    }

    public function testAccounts()
    {
        $raw = DB::table('accounts')->where('user_id',Auth::user()->id)->count();
        $count = Auth::user()->accounts()->count();
        $this->assertEquals($raw,$count);
    }

    public function testPredictables()
    {
        $raw = DB::table('predictables')->where('user_id',Auth::user()->id)->count();
        $count = Auth::user()->predictables()->count();
        $this->assertEquals($raw,$count);
    }

    public function testPiggybanks()
    {
        $raw = DB::table('piggybanks')->where('user_id',Auth::user()->id)->count();
        $count = Auth::user()->piggybanks()->count();
        $this->assertEquals($raw,$count);
    }

    public function testSettings()
    {
        $raw = DB::table('settings')->where('user_id',Auth::user()->id)->count();
        $count = Auth::user()->settings()->count();
        $this->assertEquals($raw,$count);
    }

    public function testComponents()
    {
        $raw = DB::table('components')->where('user_id',Auth::user()->id)->count();
        $count = Auth::user()->components()->count();
        $this->assertEquals($raw,$count);
    }

    public function testTransactions()
    {
        $raw = DB::table('transactions')->where('user_id',Auth::user()->id)->count();
        $count = Auth::user()->transactions()->count();
        $this->assertEquals($raw,$count);
    }

    public function testTransfers()
    {
        $raw = DB::table('transfers')->where('user_id',Auth::user()->id)->count();
        $count = Auth::user()->transfers()->count();
        $this->assertEquals($raw,$count);
    }

    public function testLimits()
    {
        $raw = DB::table('limits')->leftJoin(
            'components', 'components.id', '=',
            'limits.component_id'
        )->where('components.user_id', Auth::user()->id)->count();
        $count = Auth::user()->limits()->count();
        $this->assertEquals($raw, $count);
    }

    public function testGetAuthIdentifier()
    {
        $this->assertEquals(Auth::user()->id,Auth::user()->getAuthIdentifier());
    }

    public function testGetAuthPassword()
    {
        $this->assertEquals(Auth::user()->password,Auth::user()->getAuthPassword());
    }

    public function testGetReminderEmail()
    {
        $email = Auth::user()->email;
        $this->assertEquals($email, Auth::user()->getReminderEmail());
    }

    public function testSendRegistrationMail()
    {
        $result = Auth::user()->sendRegistrationMail();
        $this->assertTrue($result);
        // TODO implement
    }

    public function testSendPasswordMail()
    {
        $result = Auth::user()->sendPasswordMail();
        $this->assertTrue($result);
    }

    public function testSendResetMail()
    {
        $result = Auth::user()->sendResetMail();
        $this->assertTrue($result);
    }

    public function testGetDates()
    {
        $this->assertInstanceOf('\Carbon\Carbon', Auth::user()->updated_at);
        $this->assertInstanceOf('\Carbon\Carbon', Auth::user()->created_at);
    }

} 