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

    /**
     * Redirect user if not logged in.
     *
     * @covers HomeController::index()
     */
    public function testIndexLogin() {
        $user = User::whereUsername('admin');
        $this->be($user);

        $this->action('GET', 'HomeController@index');
        $this->assertRedirectedToRoute('home');
    }

}