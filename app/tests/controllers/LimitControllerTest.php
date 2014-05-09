<?php

use Carbon\Carbon as Carbon;

class LimitControllerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $user = User::where('username', 'admin')->first();
        $this->be($user);
    }

    /**
     * @covers LimitController::add
     */
    public function testAdd()
    {
        // find a component:
        $component = DB::table('components')->first();
        $date = new Carbon;
        $date->startOfMonth();

        $response = $this->action(
            'GET', 'LimitController@add', [$component->id, $date->format('Y'), $date->format('m')]
        );
        $view = $response->original;

        $count = Account::count() + 1;

        $this->assertResponseOk();
        $this->assertSessionHas('previous');
        $this->assertEquals($component->id, $view['component']->id);
        $this->assertEquals($date->format('Ymd'), $view['date']->format('Ymd'));
        $this->assertCount($count, $view['accounts']);

    }

    /**
     * @covers LimitController::postAdd
     */
    public function testPostAdd()
    {
        // add a limit, then delete it.
        $component = DB::table('components')->first();
        $date = new Carbon;
        $date->startOfMonth();

        // new limit information, no account
        $newLimit = [
            'account_id' => 0,
            'amount' => 500
        ];
        $count = Limit::count();
        $this->call(
            'POST', '/home/limit/add/'.$component->id.'/'.$date->format('Y/m'),$newLimit
        );

        $newCount = Limit::count();

        $this->assertResponseStatus(302);
        $this->assertSessionHas('success');
        $this->assertEquals($count+1,$newCount);

        // delete it again:
        DB::table('limits')->where('component_id',$component->id)->delete();



    }

    /**
     * @covers LimitController::postAdd
     */
    public function testPostAddFailsValidation()
    {
        // add a limit, then delete it.
        $component = DB::table('components')->first();
        $date = new Carbon;
        $date->startOfMonth();

        // new limit information, no account
        $newLimit = [
            'account_id' => 0,
            'amount' => null
        ];
        $count = Limit::count();
        $this->call(
            'POST', '/home/limit/add/'.$component->id.'/'.$date->format('Y/m'),$newLimit
        );

        $newCount = Limit::count();

        $this->assertResponseStatus(302);
        $this->assertSessionHas('error');
        $this->assertEquals($count,$newCount);

        // delete it again:
        DB::table('limits')->where('component_id',$component->id)->delete();



    }

    /**
     * @covers LimitController::postAdd
     */
    public function testPostAddFailsTrigger()
    {
        // add a limit, then delete it.
        $component = DB::table('components')->first();
        $date = new Carbon;
        $date->startOfMonth();

        // quickly create this exact same limit
        // that will alert the trigger.
        Limit::create(
            [
                'account_id' => null,
                'amount' => 500,
                'component_id' => $component->id,
                'date' => $date->format('Y-m-d')
            ]
        );

        // new limit information, no account
        $newLimit = [
            'account_id' => 0,
            'amount' => 500
        ];
        $count = Limit::count();
        $this->call(
            'POST', '/home/limit/add/'.$component->id.'/'.$date->format('Y/m'),$newLimit
        );

        $newCount = Limit::count();

        $this->assertResponseStatus(302);
        $this->assertSessionHas('error');
        $this->assertEquals($count,$newCount);

        // delete it again:
        DB::table('limits')->where('component_id',$component->id)->delete();



    }

    /**
     * @covers LimitController::postAdd
     */
    public function testPostAddWithAccount()
    {
        // add a limit, then delete it.
        $component = DB::table('components')->first();
        $account = DB::table('accounts')->first();
        $date = new Carbon;
        $date->startOfMonth();

        // new limit information, no account
        $newLimit = [
            'account_id' => $account->id,
            'amount' => 500
        ];
        $count = Limit::count();
        $this->call(
            'POST', '/home/limit/add/'.$component->id.'/'.$date->format('Y/m'),$newLimit
        );

        $newCount = Limit::count();

        $this->assertResponseStatus(302);
        $this->assertSessionHas('success');
        $this->assertEquals($count+1,$newCount);

        // delete it again:
        DB::table('limits')->where('component_id',$component->id)->delete();



    }

    /**
     * @covers LimitController::postAdd
     */
    public function testPostAddWithInvalidAccount()
    {
        // add a limit, then delete it.
        $component = DB::table('components')->first();
        $date = new Carbon;
        $date->startOfMonth();

        // new limit information, no account
        $newLimit = [
            'account_id' => -1,
            'amount' => 500
        ];
        $count = Limit::count();
        $this->call(
            'POST', '/home/limit/add/'.$component->id.'/'.$date->format('Y/m'),$newLimit
        );

        $newCount = Limit::count();

        $this->assertResponseStatus(302);
        $this->assertSessionHas('error');
        $this->assertEquals($count,$newCount);

        // delete it again:
        DB::table('limits')->where('component_id',$component->id)->delete();
    }

    /**
     * @covers LimitController::edit
     */
    public function testEdit()
    {
        // create a limit first:
        $component = DB::table('components')->first();
        $date = new Carbon;
        $limit = Limit::create([
                'component_id' => $component->id,
                'amount' => 500,
                'date' => $date->format('Y-m-d'),
                'account_id' => null
            ]);

        $response = $this->action('GET', 'LimitController@edit', $limit->id);
        $view = $response->original;

        $count = Account::count() + 1;

        $this->assertResponseOk();
        $this->assertSessionHas('previous');
        $this->assertCount($count,$view['accounts']);
        $this->assertEquals($component->id,$view['component']->id);
        $this->assertEquals($limit->id,$view['limit']->id);

        // delete limit again.
        $limit->delete();


    }

    /**
     * @covers LimitController::postEdit
     */
    public function testPostEdit()
    {
        // create a limit:
        $date = new Carbon;
        $component = DB::table('components')->first();
        $limit = Limit::create([
                'component_id' => $component->id,
                'amount' => 500,
                'date' => $date->format('Y-m-d'),
                'account_id' => null
            ]);

        // new info for limit:
        $newData = [
            'amount' => 1000
        ];
        // post!
        $this->call('POST', '/home/limit/edit/'.$limit->id,$newData);

        // should be updated:
        $updated = Limit::find($limit->id);

        $this->assertResponseStatus(302);
        $this->assertSessionHas('success');
        $this->assertEquals($newData['amount'],$updated->amount);

        $limit->delete();

    }

    /**
     * @covers LimitController::postEdit
     */
    public function testPostEditWithAccount()
    {
        // create a limit:
        $date = new Carbon;
        $component = DB::table('components')->first();
        $limit = Limit::create([
                'component_id' => $component->id,
                'amount' => 500,
                'date' => $date->format('Y-m-d'),
                'account_id' => null
            ]);
        $account = Account::first();

        // new info for limit:
        $newData = [
            'amount' => 1000,
            'account_id' => $account->id
        ];
        // post!
        $this->call('POST', '/home/limit/edit/'.$limit->id,$newData);

        // should be updated:
        $updated = Limit::find($limit->id);

        $this->assertResponseStatus(302);
        $this->assertSessionHas('success');
        $this->assertEquals($newData['amount'],$updated->amount);

        $limit->delete();

    }

    /**
     * @covers LimitController::postEdit
     */
    public function testPostEditWithInvalidAccount()
    {
        // create a limit:
        $date = new Carbon;
        $component = DB::table('components')->first();
        $limit = Limit::create([
                'component_id' => $component->id,
                'amount' => 500,
                'date' => $date->format('Y-m-d'),
                'account_id' => null
            ]);

        // new info for limit:
        $newData = [
            'amount' => 1000,
            'account_id' => -1
        ];
        // post!
        $this->call('POST', '/home/limit/edit/'.$limit->id,$newData);

        // should be updated:
        $updated = Limit::find($limit->id);

        $this->assertResponseStatus(302);
        $this->assertSessionHas('error');

        $limit->delete();

    }

    /**
     * @covers LimitController::postEdit
     */
    public function testPostEditFailsValidator()
    {
        // create a limit:
        $date = new Carbon;
        $component = DB::table('components')->first();
        $limit = Limit::create([
                'component_id' => $component->id,
                'amount' => 500,
                'date' => $date->format('Y-m-d'),
                'account_id' => null
            ]);

        // new info for limit:
        $newData = [
            'amount' => null
        ];
        // post!
        $this->call('POST', '/home/limit/edit/'.$limit->id,$newData);

        // should be updated:
        $updated = Limit::find($limit->id);

        $this->assertResponseStatus(302);
        $this->assertSessionHas('error');
        $limit->delete();

    }

    /**
     * @covers LimitController::delete
     */
    public function testDelete()
    {
        // create a limit:
        $date = new Carbon;
        $component = DB::table('components')->first();
        $limit = Limit::create([
                'component_id' => $component->id,
                'amount' => 500,
                'date' => $date->format('Y-m-d'),
                'account_id' => null
            ]);

        $response = $this->action('GET', 'LimitController@delete', $limit->id);
        $view = $response->original;

        $this->assertResponseOk();
        $this->assertSessionHas('previous');
        $this->assertEquals($limit->id,$view['limit']->id);
        $this->assertEquals($component->id,$view['component']->id);

        // delete it
        $limit->delete();

    }

    /**
     * @covers LimitController::postDelete
     */
    public function testPostDelete()
    {
        // create a limit:
        $date = new Carbon;
        $component = DB::table('components')->first();
        $limit = Limit::create([
                'component_id' => $component->id,
                'amount' => 500,
                'date' => $date->format('Y-m-d'),
                'account_id' => null
            ]);

        $count = Limit::count();

        // delete it:
        $this->action('POST', 'LimitController@delete', $limit->id);
        $newCount = Limit::count();
        $this->assertResponseStatus(302);
        $this->assertEquals($count-1,$newCount);
        $this->assertSessionHas('success');
    }

} 