<?php


class PredictableModelTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $user = User::where('username', 'test')->first();
        $this->be($user);
    }

    public function testUser()
    {
        // TODO implement
        $predictable = Auth::user()->predictables()->first();
        $this->assertEquals(Auth::user()->id, $predictable->user()->first()->id);
    }

    public function testScopeActive()
    {
        // TODO implement
        $raw = DB::table('predictables')->where('inactive', 0)->count();
        $count = Predictable::active()->count();
        $this->assertEquals($raw, $count);
    }

    public function testGetAllEmptyAttribute()
    {
        $predictables = Auth::user()->predictables()->get();
        $found = false;
        foreach ($predictables as $p) {
            $count = DB::table('component_predictable')->where('predictable_id', $p->id)->count();
            if ($count == 0) {
                $this->assertNull($p->beneficiary);
                $this->assertNull($p->category);
                $this->assertNull($p->budget);
                $found = true;
            }

        }
        if (!$found) {
            $this->assertTrue(false, 'No predictables found to test in testGetAllEmptyAttribute');
        }

        //$predictable->category;
        //$predictable->budget;
        //$predictable->beneficiary;
    }

    public function testGetAllAttributes()
    {
        $predictables = Auth::user()->predictables()->get();
        $found = false;
        foreach ($predictables as $p) {
            $count = DB::table('component_predictable')->where('predictable_id', $p->id)->count();
            if ($count == 3) {
                $found = true;
                $this->assertNotNull($p->beneficiary);
                $this->assertNotNull($p->category);
                $this->assertNotNull($p->budget);

                $this->assertEquals('beneficiary',$p->beneficiary->type);
                $this->assertEquals('budget',$p->budget->type);
                $this->assertEquals('category',$p->category->type);
            }
        }
        if (!$found) {
            $this->assertTrue(false, 'No predictables found to test in testGetAllAttributes');
        }
    }

    public function testTransactions()
    {
        $p = Auth::user()->predictables()->first();
        $count = DB::table('transactions')->where('predictable_id',$p->id)->count();
        $this->assertEquals($count,$p->transactions()->count());
    }

    public function testGetDescriptionAttribute()
    {
        $p = Auth::user()->predictables()->first();
        $arr = $p->toArray();
        $this->assertEquals($arr['description'],$p->description);
    }

    public function testSetDescriptionAttribute()
    {
        $p = Auth::user()->predictables()->first();
        $p->description = null;
        $this->assertNull($p->description);

        $p->description = 'Hallo!';
        $this->assertEquals('Hallo!',$p->description);
    }

    public function testGetDates()
    {
        $predictable = Predictable::first();
        $this->assertInstanceOf('\Carbon\Carbon', $predictable->updated_at);
        $this->assertInstanceOf('\Carbon\Carbon', $predictable->created_at);
    }


} 