<?php

class UserControllerTest extends TestCase
{
    public function testLogin()
    {
        $response = $this->call('GET', 'login');
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals('Login', $view['title']);
    }

    public function testPostLogin()
    {
        $data = [
            'username' => 'test',
            'password' => 'test'
        ];
        $this->call('POST', 'login',$data);
        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('home');
    }

    public function testPostFailedLogin()
    {
        $data = [
            'username' => 'test',
            'password' => 'test!'
        ];
        $response = $this->call('POST', 'login',$data);
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals('Incorrect login details', $view['warning']);
    }

    public function testLogout()
    {
        $this->call('GET', 'logout');
        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('index');
    }

    public function testRegister()
    {
        $response = $this->call('GET', 'register');
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals('Register', $view['title']);
    }
    public function testPostRegister()
    {
        $data = ['email' => 'random@nder.be'];
        $response = $this->call('POST', 'register',$data);
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals('Registered!', $view['title']);
    }
    public function testPostRegisterInvalid()
    {
        $data = ['email' => 'yo moma'];
        $response = $this->call('POST', 'register',$data);
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals('Register', $view['title']);
        $this->assertEquals('Invalid e-mail address.', $view['warning']);
    }

    public function testActivate()
    {
        $user = User::where('email','random@nder.be')->first();
        $code = $user->activation;

        $response = $this->call('GET', 'activate/'.$code);
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals('Activated', $view['title']);

    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function testActivateLoggedIn()
    {
        $data = [
            'username' => 'test',
            'password' => 'test'
        ];
        $this->call('POST', 'login',$data);
        $this->call('GET', 'activate/12434');
        $this->assertResponseStatus(500);


    }
    public function testActivateInvalid()
    {
        $response = $this->call('GET', 'activate/12434');
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals('Activated', $view['title']);
    }



    public function testReset()
    {
        $response = $this->call('GET', 'reset');
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals('Reset password', $view['title']);
    }

    public function testPostReset()
    {
        $data = [
            'username' => 'random@nder.be',
        ];
        $response = $this->call('POST', 'reset',$data);
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals('Sent!', $view['title']);
    }

    public function testPostResetFailed()
    {
        $data = [
            'username' => 'testblabla',
        ];
        $response = $this->call('POST', 'reset',$data);
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals('Sent!', $view['title']);
    }

    public function testResetme()
    {
        $user = User::where('username','random@nder.be')->first();
        $code = $user->reset;
        $response = $this->call('GET', 'resetme/'.$code);
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals('Reset!', $view['title']);

    }
    public function testResetmeInvalid()
    {
        $response = $this->call('GET', 'resetme/blabla');
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals('Reset!', $view['title']);


    }

    public static function tearDownAfterClass()
{
DB::table('users')->where('username','random@nder.be')->delete();
}

} 