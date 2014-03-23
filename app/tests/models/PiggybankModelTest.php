<?php


class PiggybankModelTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $user = User::where('username', 'test')->first();
        $this->be($user);
    }

    public function testUser()
    {
        $piggy = Auth::user()->piggybanks()->first();
        $this->assertEquals(Auth::user()->id, $piggy->user()->first()->id);
    }

    public function testGetNameAttribute()
    {
        $piggy = Auth::user()->piggybanks()->first();
        $piggy->name = 'Bla bla';
        $this->assertEquals('Bla bla', $piggy->name);
    }

    public function testPctFilled()
    {
        $piggy = Auth::user()->piggybanks()->whereNotNull('target')->first();
        $pct = round(($piggy->amount / $piggy->target) * 100);
        $expected = $pct >= 100 ? 100 : $pct;

        $this->assertEquals($expected, $piggy->pctFilled());
    }

    public function testPctFilledEmpty()
    {
        $piggy = Auth::user()->piggybanks()->whereNull('target')->first();
        $expected = 0;

        $this->assertEquals($expected, $piggy->pctFilled());

    }
}