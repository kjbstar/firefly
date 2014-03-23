<?php


class LimitModelTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $user = User::where('username', 'test')->first();
        $this->be($user);
    }

    public function testComponent()
    {
        $limit = Limit::first();
        $component = DB::table('components')->find($limit->component_id);
        $this->assertEquals($component->id,$limit->component()->first()->id);

    }

    public function testScopeInMonth()
    {
        // get a date:
        $dateLimit = Limit::first();
        // find a limit for this date:
        $raw = DB::table('limits')->where('date',$dateLimit->date->format('Y-m-d'))->count();
        $count = Limit::inMonth($dateLimit->date)->count();
        $this->assertEquals($raw,$count);


    }

    public function testGetDates()
    {
        $limit = Limit::first();
        $this->assertInstanceOf('\Carbon\Carbon', $limit->created_at);
        $this->assertInstanceOf('\Carbon\Carbon', $limit->updated_at);
        $this->assertInstanceOf('\Carbon\Carbon', $limit->date);
    }

    public function testGetAmountAttribute()
    {
        $limit = Limit::first();
        $this->assertTrue(is_float($limit->amount));
    }

} 