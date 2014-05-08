<?php

class HomeControllerTest extends TestCase
{

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
        $user = User::whereUsername('admin')->first();
        $this->be($user);

        $this->action('GET', 'HomeController@index');
        $this->assertRedirectedToRoute('home');
    }

    /**
     * Run the home page and test some basic information.
     */
    public function testHomeBasic() {
        $user = User::whereUsername('admin')->first();
        $this->be($user);

        $response = $this->action('GET', 'HomeController@home');
        $view = $response->original;

        $this->assertResponseOk();

        $this->assertEquals('Home',$view['title']);


    }

}