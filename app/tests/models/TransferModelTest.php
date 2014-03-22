<?php


class TransferModelTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();
        $user = User::where('username', 'test')->first();
        $this->be($user);
    }

    public function testUser()
    {
        $transfer = Auth::user()->transfers()->first();
        $user = $transfer->user()->first();
        $this->assertEquals($user->id, Auth::user()->id);
    }
} 