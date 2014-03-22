<?php


class UserModelTest extends TestCase {
    public function setUp()
    {
        parent::setUp();
        $user = User::where('username', 'test')->first();
        $this->be($user);
    }


    public function testLimits() {
        $raw = DB::table('limits')->leftJoin('components','components.id','=',
            'limits.component_id')->where('components.user_id',Auth::user()->id)->count();
        $count = Auth::user()->limits()->count();
        $this->assertEquals($raw,$count);
    }

    public function testGetReminderEmail() {
        $email = Auth::user()->email;
        $this->assertEquals($email,Auth::user()->getReminderEmail());
    }

} 