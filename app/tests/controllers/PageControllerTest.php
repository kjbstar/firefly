<?php

class PageControllerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $user = User::where('username', 'admin')->first();
        $this->be($user);
    }

    public function testFlush()
    {
        $this->action('GET', 'PageController@flush');
        $this->assertResponseStatus(302);

    }

    public function testRecalculate()
    {
        $this->action('GET', 'PageController@recalculate');
        $this->assertResponseStatus(302);

    }
}