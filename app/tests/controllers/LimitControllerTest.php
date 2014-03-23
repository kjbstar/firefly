<?php

class LimitControllerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $user = User::where('username', 'test')->first();
        $this->be($user);
    }
    private $amount = 123.45;

    public function testAddLimit()
    {
        Route::enableFilters();
        $budget = Auth::user()->components()->where('type', 'budget')->first();
        $response = $this->call(
            'GET', 'home/budget/limit/add/' . $budget->id . '/' . date('Y/m')
        );
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals($budget->name, $view['object']->name);
        $this->assertSessionHas('previous');
        $this->assertEquals(
            date('Y-m-') . '01', $view['date']->format('Y-m-d')
        );
        Route::disableFilters();
    }

    public function testPostAddLimit()
    {
        $count = Limit::count();
        $budget = Auth::user()->components()->where('type', 'budget')->first();
        $data = ['amount' => $this->amount];
        $this->call(
            'POST', 'home/budget/limit/add/' . $budget->id . '/' . date('Y/m'),
            $data
        );
        $newCount = Limit::count();
        $this->assertEquals($count+1,$newCount);
        $this->assertResponseStatus(302);
        $this->assertSessionHas('success');
        $this->assertRedirectedToRoute('index');
    }

    public function testInvalidPostAddLimit()
    {

        $count = Limit::count();
        $budget = Auth::user()->components()->where('type', 'budget')->first();
        $data = ['amount' => 0];
        $this->call(
            'POST', 'home/budget/limit/add/' . $budget->id . '/' . date('Y/m'),
            $data
        );
        $newCount = Limit::count();
        $this->assertResponseStatus(302);
        $this->assertEquals($count,$newCount);
        $this->assertSessionHas('error');
        $this->assertRedirectedToRoute('budgetoverview', $budget->id);
    }

    public function testEditLimit()
    {
        $limit = Limit::where('amount',$this->amount)->first();
        $response = $this->call('GET', 'home/budget/limit/edit/' . $limit->id);
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals($limit->amount, $view['limit']->amount);
        $this->assertSessionHas('previous');
    }

    public function testPostEditLimit()
    {
        $limit = Limit::where('amount',$this->amount)->first();
        $data = ['amount' => $this->amount];
        $this->call('POST', 'home/budget/limit/edit/' . $limit->id, $data);
        $this->assertResponseStatus(302);
        $this->assertSessionHas('success');
        $this->assertRedirectedToRoute('index');
    }

    public function testPostEditLimitNoAmount()
    {
        $limit = Limit::first();
        $component = $limit->component()->where('type', 'budget')->first();
        $data = ['amount' => null];
        $this->call('POST', 'home/budget/limit/edit/' . $limit->id, $data);
        $this->assertResponseStatus(302);
        $this->assertSessionHas('error');
        $this->assertRedirectedToRoute('budgetoverview', $component->id);
    }

    public function testDeleteLimit()
    {
        $limit = Limit::where('amount',$this->amount)->first();
        $response = $this->call(
            'GET', 'home/budget/limit/delete/' . $limit->id
        );
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals($limit->date, $view['date']);
        $this->assertSessionHas('previous');
    }

    public function testPostDeleteLimit()
    {
        $count = Limit::count();
        $limit = Limit::where('amount',$this->amount)->first();
        $this->call('POST', 'home/budget/limit/delete/' . $limit->id);
        $this->assertResponseStatus(302);
        $newCount = Limit::count();
        $this->assertEquals($count-1,$newCount);
        $this->assertRedirectedToRoute('index');
        $this->assertSessionHas('success');
    }

} 