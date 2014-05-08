<?php

class HomeControllerTest extends TestCase
{

    public function testSomethingIsTrue()
    {
        $this->assertTrue(true);
    }

    /**
     * Redirect user if not logged in.
     *
     * @covers HomeController::index()
     */
    public function testIndexNoLogin() {
        $this->action('GET', 'HomeController@index');
        $this->assertRedirectedToRoute('login');
    }

}