<?php


class TransferModelTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();
        $user = User::where('username', 'test')->first();
        $this->be($user);
    }

    public function testGetEmptyAttributes()
    {
        $transfers = Auth::user()->transfers()->get();
        $found = false;
        foreach ($transfers as $t) {
            $count = DB::table('component_transfer')->where('transfer_id', $t->id)->count();
            if ($count == 0) {
                $this->assertNull($t->beneficiary);
                $this->assertNull($t->category);
                $this->assertNull($t->budget);
                $found = true;
                break;
            }

        }
        if (!$found) {
            $this->assertTrue(false, 'No transfers found to test in testGetEmptyAttributes');
        }
    }

    public function testGetFilledAttributes()
    {
        $transfers = Auth::user()->transfers()->get();
        $found = false;
        foreach ($transfers as $t) {
            $count = DB::table('component_transfer')->where('transfer_id', $t->id)->count();
            if ($count >= 3) {
                $this->assertNotNull($t->beneficiary);
                $this->assertNotNull($t->category);
                $this->assertNotNull($t->budget);

                $this->assertEquals('beneficiary', $t->beneficiary->type);
                $this->assertEquals('budget', $t->budget->type);
                $this->assertEquals('category', $t->category->type);
                $found = true;
                break;
            }

        }
        if (!$found) {
            $this->assertTrue(false, 'No transfers found to test in testGetFilledAttributes');
        }
    }

    public function testAccountfrom()
    {
        $transfer = Auth::user()->transfers()->first();
        $this->assertEquals($transfer->accountfrom_id,$transfer->accountfrom()->first()->id);
    }

    public function testAccountto()
    {
        $transfer = Auth::user()->transfers()->first();
        $this->assertEquals($transfer->accountto_id,$transfer->accountto()->first()->id);
    }


    public function testUser()
    {
        $transfer = Auth::user()->transfers()->first();
        $user = $transfer->user()->first();
        $this->assertEquals($user->id, Auth::user()->id);
    }

    public function testScopeInMonth()
    {
        $date = new \Carbon\Carbon();
        $date->subYear();
        $raw = Auth::user()->transfers()->where(
            DB::Raw('DATE_FORMAT(`date`,"%m-%Y")'), '=',
            $date->format('m-Y')
        )->count();
        $count = Auth::user()->transfers()->inMonth($date)->count();
        $this->assertEquals($raw, $count);
    }

    public function testGetDescriptionAttribute()
    {
        $transfer = Auth::user()->transfers()->first();
        $transfer->description = 'Bla bla';
        $this->assertEquals('Bla bla',$transfer->description);
        $transfer->description = null;
        $this->assertNull($transfer->description);
    }

    public function testGetDates()
    {
        $transfer = Auth::user()->transfers()->first();
        $this->assertInstanceOf('\Carbon\Carbon', $transfer->updated_at);
        $this->assertInstanceOf('\Carbon\Carbon', $transfer->created_at);
        $this->assertInstanceOf('\Carbon\Carbon', $transfer->date);
    }

    public function testAttachComponent()
    {
        $transfer = Auth::user()->transfers()->orderBy(DB::Raw('RAND()'))->first();
        $count = DB::table('component_transfer')->where('transfer_id', $transfer->id)->count();
        // attach a component:
        $budget = Auth::user()->components()->where('type', 'budget')->first();
        $transfer->attachComponent($budget);
        $newCount = DB::table('component_transfer')->where('transfer_id', $transfer->id)->count();
        $this->assertEquals($count + 1, $newCount);
        $this->assertEquals($budget->id, $transfer->budget->id);

    }

    public function testScopeBeforeDate()
    {
        $date = new \Carbon\Carbon();
        $date->subYear();
        $raw = Auth::user()->transfers()->where('date', '<=', $date->format('Y-m-d'))->count();
        $count = Auth::user()->transfers()->beforeDate($date)->count();
        $this->assertEquals($raw, $count);

    }

    public function testScopeInYear()
    {
        $date = new \Carbon\Carbon();
        $date->subYear();
        $raw = Auth::user()->transfers()->where(DB::Raw('DATE_FORMAT(`date`,"%Y")'), '=', $date->format('Y'))->count(
        );
        $count = Auth::user()->transfers()->inYear($date)->count();
        $this->assertEquals($raw, $count);
    }


    public function testAttachEmptyComponent()
    {
        $transfer = Auth::user()->transfers()->orderBy(DB::Raw('RAND()'))->first();
        $count = DB::table('component_transfer')->where('transfer_id', $transfer->id)->count();
        // attach a component:
        $transfer->attachComponent(null);
        $newCount = DB::table('component_transfer')->where('transfer_id', $transfer->id)->count();
        $this->assertEquals($count, $newCount);
        $this->assertNull($transfer->budget);

    }
} 