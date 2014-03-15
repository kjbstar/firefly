<?php

/**
 * Created by PhpStorm.
 * User: sander
 * Date: 12/03/14
 * Time: 12:05
 */
class HomeControllerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $user = User::where('username', 'test')->first();
        $this->be($user);
    }

    public function testShowIndex()
    {
        $this->client->request('GET', '/');
        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('home');
        $this->assertTrue(true);
    }

    public function testShowHome()
    {
        $date = new Carbon\Carbon;

        // count some stuff:
        $transfers = Auth::user()->transfers()->take(5)->inMonth($date)->count();
        $transactions = Auth::user()->transactions()->take(5)->inMonth($date)->count();
        $accounts = Auth::user()->accounts()->count();
        $predictables = Auth::user()->predictables()->count();

        $response = $this->call('GET', '/home');
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals($view['title'], 'Home');
        $this->assertCount($accounts, $view['accounts']);
        $this->assertCount(4, $view['allowance']);
        $this->assertCount($predictables, $view['predictables']);
        $this->assertCount($transactions, $view['transactions']);
        $this->assertCount($transfers, $view['transfers']);

        // we know we started in 2012-01-01, it's hard coded.
        $start = new Carbon\Carbon('2012-01-01');
        $now = new Carbon\Carbon;
        $diff = $now->diffInMonths($start);
        $diff = $diff+2;

        // TODO better count
        $this->assertCount($diff, $view['history']);
        $this->assertCount(0, $view['budgets']);


    }

    public function testShowHomeForMonth()
    {

        $response = $this->call('GET', '/home/' . date('Y/m'));
        $date = new Carbon\Carbon;

        $transfers = Auth::user()->transfers()->take(5)->inMonth($date)->count();
        $transactions = Auth::user()->transactions()->take(5)->inMonth($date)->count();
        $accounts = Auth::user()->accounts()->count();
        $predictables = Auth::user()->predictables()->count();


        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals($view['title'], 'Home');
        $this->assertCount($accounts, $view['accounts']);
        $this->assertCount(4, $view['allowance']);
        $this->assertCount($predictables, $view['predictables']);
        $this->assertCount($transactions, $view['transactions']);
        $this->assertCount($transfers, $view['transfers']);

        // we know we started in 2012-01-01, it's hard coded.
        $start = new Carbon\Carbon('2012-01-01');
        $now = new Carbon\Carbon;
        $diff = $now->diffInMonths($start);
        $diff = $diff+2;

        // TODO better count
        $this->assertCount($diff, $view['history']);
        $this->assertCount(0, $view['budgets']);

    }

    public function testPredict()
    {
        $response = $this->call('GET', '/home/predict/' . date('Y/m/d'));
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertCount(3, $view['prediction']);
        $carbon = new Carbon\Carbon(date('Y-m-d'));
        $this->assertEquals($carbon, $view['date']);
    }
} 