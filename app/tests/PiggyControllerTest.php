<?php

class PiggyControllerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $user = User::where('username', 'test')->first();
        $this->be($user);
    }

    public function testIndexBeforeSetting()
    {
        $this->call('GET', 'home/piggy');
        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('piggyselect');
    }

    public function testAddBeforeSetting()
    {
        $this->call('GET', 'home/piggy/add');
        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('piggyselect');
    }

    public function testSelectAccount()
    {
        $response = $this->call('GET', 'home/piggy/select');
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals('Piggy banks', $view['title']);
        $accounts = Account::count();
        $this->assertCount($accounts, $view['accounts']);

    }

    public function testPostSelectAccount()
    {
        // TODO validate setting
        $setting = Auth::user()->settings()->where('name', 'piggyAccount')
            ->first();
        $this->assertEquals(0,$setting->value);
        $account = Account::first()->id;
        $data = ['account' => $account];
        $this->call('POST', 'home/piggy/select', $data);
        $this->assertResponseStatus(302);
        $this->assertSessionHas('success');
        $setting = Auth::user()->settings()->where('name', 'piggyAccount')
            ->first();
        $this->assertEquals($account,$setting->value);

        $this->assertRedirectedToRoute('piggy');
    }

    public function testPostSelectInvalidAccount()
    {
        $setting = Auth::user()->settings()->where('name', 'piggyAccount')
            ->first();
        $data = ['account' => 0];
        $this->call('POST', 'home/piggy/select', $data);
        $this->assertResponseStatus(302);
        $this->assertSessionHas('error');
        $this->assertNotEquals($setting->value,0);
        $this->assertRedirectedToRoute('piggyselect');
    }


    /**
     * @dependsOn testSelectAccount
     * @dependsOn testPostSelectAccount
     */
    public function testIndex()
    {
        $response = $this->call('GET', 'home/piggy');
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals('Piggy banks', $view['title']);
        $piggies = Piggybank::count();
        $this->assertCount($piggies, $view['piggies']);
    }

    public function testAdd()
    {
        $response = $this->call('GET', 'home/piggy/add');
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals('Add new piggy bank', $view['title']);

    }

    public function testEmptyPostAdd()
    {
        $count = Auth::user()->piggybanks()->count();
        $this->call('POST', 'home/piggy/add');
        $newCount = Auth::user()->piggybanks()->count();
        $this->assertEquals($count,$newCount);
        $this->assertResponseStatus(302);
        $this->assertHasOldInput();
        $this->assertSessionHas('error');
        $this->assertRedirectedToRoute('addpiggybank');
    }

    public function testFilledPostAdd()
    {
        $count = Auth::user()->piggybanks()->count();
        $data = ['name' => 'NogEenTest', 'target' => 200];
        $this->call('POST', 'home/piggy/add', $data);
        $newCount = Auth::user()->piggybanks()->count();
        $this->assertResponseStatus(302);
        $this->assertEquals($count+1,$newCount);
        $this->assertSessionHas('success');
        $this->assertRedirectedToRoute('index');

    }

    public function testInvalidPostAdd()
    {
        $count = Auth::user()->piggybanks()->count();
        $data = ['target' => 200];
        $this->call('POST', 'home/piggy/add', $data);
        $newCount = Auth::user()->piggybanks()->count();
        $this->assertResponseStatus(302);
        $this->assertHasOldInput();
        $this->assertEquals($count,$newCount);
        $this->assertRedirectedToRoute('addpiggybank');


    }


    public function testDelete()
    {
        $pig = Piggybank::first();
        $response = $this->call('GET', 'home/piggy/delete/' . $pig->id);
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals('Delete piggy bank ' . $pig->name, $view['title']);
    }

    public function testPostDelete()
    {
        $count = Auth::user()->piggybanks()->count();
        $pig = Piggybank::first();
        $this->call('POST', 'home/piggy/delete/' . $pig->id);
        $newCount = Auth::user()->piggybanks()->count();
        $this->assertResponseStatus(302);
        $this->assertEquals($count-1,$newCount);
        $this->assertSessionHas('success');
        $this->assertRedirectedToRoute('index');

    }


    public function testEdit()
    {
        $pig = Piggybank::first();
        $response = $this->call('GET', 'home/piggy/edit/' . $pig->id);
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals(
            'Edit piggy bank "' . $pig->name . '"', $view['title']
        );
    }

    public function testPostEdit()
    {
        $pig = Piggybank::first();
        $data = ['amount' => 100, 'target' => '123', 'name' => 'Edited name'];
        $response = $this->call('POST', 'home/piggy/edit/' . $pig->id, $data);
        $this->assertResponseStatus(302);
        $this->assertSessionHas('success');
        $this->assertRedirectedToRoute('index');
    }

    public function testPostInvalidEdit()
    {
        $pig = Piggybank::first();
        $data = ['amount' => 100, 'target' => 123,];
        $response = $this->call('POST', 'home/piggy/edit/' . $pig->id, $data);
        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('editpiggy', $pig->id);
    }

    public function testUpdateAmount()
    {
        $pig = Piggybank::first();
        $response = $this->call('GET', 'home/piggy/amount/' . $pig->id);
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals($pig->name, $view['pig']->name);

    }

    public function testPostUpdateAmount()
    {
        $pig = Piggybank::first();
        $data = ['amount' => -20,];
        $response = $this->call('POST', 'home/piggy/amount/' . $pig->id, $data);
        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('index');
    }
} 