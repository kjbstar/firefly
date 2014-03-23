<?php


class TransferModelTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();
        $user = User::where('username', 'test')->first();
        $this->be($user);
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
} 