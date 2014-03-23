<?php


class ComponentModelTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $user = User::where('username', 'test')->first();
        $this->be($user);
    }

    public function testFindOrCreateEmpty()
    {
        $result = Component::findOrCreate('budget', '');
        $this->assertNull($result);

        //$this->call('GET', 'logout');
    }

    public function testFindOrCreateWithSlash()
    {
        $result = Component::findOrCreate('budget', 'Bla/SomeSome');
        $this->assertInstanceOf('\Component', $result);
        $this->assertEquals('SomeSome', $result->name);

        //$this->call('GET', 'logout');
    }

    public function testFindExisting()
    {
        $component = Auth::user()->components()->first();
        $result = Component::findOrCreate($component->type, $component->name);
        $this->assertInstanceOf('\Component', $result);
        $this->assertEquals($component->id, $result->id);
        //$this->call('GET', 'logout');
    }

    public function testFindOrCreate()
    {
        $count = DB::table('components')->count();
        $result = Component::findOrCreate('beneficiary', 'This one is new');
        $newCount= DB::table('components')->count();
        $this->assertInstanceOf('\Component', $result);
        $this->assertEquals('This one is new', $result->name);
        $this->assertEquals('beneficiary',$result->type);
        $this->assertEquals($count+1,$newCount);
    }


    public function testFindOrCreateLoggedOut()
    {
        Auth::logout();
        $result = Component::findOrCreate('budget', 'SomeSome');
        $this->assertNull($result);
    }


    public function testParentComponent()
    {
        $component = Auth::user()->components()->whereNotNull('parent_component_id')->first();
        $this->assertEquals($component->parent_component_id, $component->parentComponent()->first()->id);
    }

    public function testChildrenComponents()
    {
        $components = Auth::user()->components()->get();
        foreach ($components as $c) {
            $raw = DB::table('components')->where('parent_component_id', $c->id)->count();
            $count = $c->childrenComponents()->count();
            $this->assertEquals($raw, $count);
        }
    }

    public function testLimits()
    {
        $components = Auth::user()->components()->get();
        foreach ($components as $c) {
            $raw = DB::table('limits')->where('component_id', $c->id)->count();
            $count = $c->limits()->count();
            $this->assertEquals($raw, $count);
        }
    }

    public function testTransactions()
    {
        $components = Auth::user()->components()->get();
        foreach ($components as $c) {
            $raw = DB::table('component_transaction')->where('component_id', $c->id)->count();
            $count = $c->transactions()->count();
            $this->assertEquals($raw, $count);
        }
    }

    public function testPredictables()
    {
        $components = Auth::user()->components()->get();
        foreach ($components as $c) {
            $raw = DB::table('component_predictable')->where('component_id', $c->id)->count();
            $count = $c->predictables()->count();
            $this->assertEquals($raw, $count);
        }
    }

    public function testUser()
    {
        $component = Auth::user()->components()->first();
        $this->assertEquals(Auth::user()->username, $component->user()->first()->username);
    }

    public function testGetNameAttribute()
    {
        $component = Auth::user()->components()->first();
        $component->name = null;
        $this->assertNull($component->name);
    }
    public function testSetNameAttribute() {
        $component = Auth::user()->components()->first();
        $component->name = 'Bla bla';
        $array = $component->toArray();
        $this->assertEquals($array['name'],$component->name);

        // TODO implement
    }

    public function testGetDates()
    {
        $component = Auth::user()->components()->first();
        $this->assertInstanceOf('\Carbon\Carbon', $component->created_at);
        $this->assertInstanceOf('\Carbon\Carbon', $component->updated_at);
    }

    public function testScopeReporting()
    {
        $raw = DB::table('components')->where('user_id', Auth::user()->id)->where('reporting', 1)->count();
        $count = Auth::user()->components()->reporting()->count();
        $this->assertEquals($raw, $count);
    }

    public static function tearDownAfterClass()
    {
        DB::table('components')->where('reporting', 0)->delete();
    }


} 