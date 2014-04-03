<?php

/**
 * Class ReportControllerTest
 */
class ReportControllerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $user = User::where('username', 'test')->first();
        $this->be($user);

    }
}