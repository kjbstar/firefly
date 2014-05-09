<?php

use Carbon\Carbon as Carbon;
class ComponentControllerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $user = User::whereUsername('admin')->first();
        $this->be($user);
    }

    /**
     * @covers ComponentController::index
     * @todo   implement this.
     */
    public function testIndex()
    {
        $type = Type::orderBy(DB::Raw('RAND()'))->first(); // should work on any type.
        $response = $this->call('GET', '/home/component/' . $type->id . '/index');
        $view = $response->original;
        $this->assertResponseOk();

        // count parent components of this type (probably zero).
        $count = DB::table('components')->whereNull('parent_component_id')->where('type_id', $type->id)->count();
        $this->assertCount($count, $view['components']);
        $this->assertEquals($type->type, $view['type']->type);

    }

    /**
     * @covers ComponentController::noComponent
     */
    public function testNoComponent()
    {
        $type = Type::orderBy(DB::Raw('RAND()'))->first(); // should work on any type.
        $response = $this->call('GET', '/home/component/' . $type->id . '/empty');
        $view = $response->original;

        $this->assertResponseOk();

        // will not count the mutations (it's hard)
        // so we avoid doing that until we must test ComponentHelper::transactionsWithoutComponent
        $this->assertEquals('Transactions without a ' . $type->type, $view['title']);
        $this->assertEquals($type, $view['type']);
    }

    /**
     * @covers ComponentController::add
     */
    public function testAdd()
    {
        $type = Type::orderBy(DB::Raw('RAND()'))->first(); // should work on any type.
        $response = $this->call('GET', '/home/component/' . $type->id . '/add');
        $view = $response->original;

        $this->assertResponseOk();
        $this->assertSessionHas('previous');

        $this->assertEquals('Add new ' . $type->type, $view['title']);
    }

    /**
     * @covers ComponentController::add
     */
    public function testAddWithOldInput()
    {
        $oldData = [
            'name' => 'Old input (new component)',
        ];
        $this->session(['_old_input' => $oldData]);
        $type = Type::orderBy(DB::Raw('RAND()'))->first(); // should work on any type.
        $response = $this->call('GET', '/home/component/' . $type->id . '/add');
        $view = $response->original;

        $this->assertResponseOk();

        $this->assertEquals('Add new ' . $type->type, $view['title']);
        $this->assertEquals($oldData['name'], $view['prefilled']['name']);
    }

    /**
     * @covers ComponentController::postAdd
     */
    public function testPostAdd()
    {
        $newComponent = [
            'name'                => 'New component for test',
            'parent_component_id' => null,
            'reporting'           => null
        ];
        $type = Type::orderBy(DB::Raw('RAND()'))->first(); // should work on any type.
        $count = DB::table('components')->whereTypeId($type->id)->count();
        $this->call('POST', '/home/component/' . $type->id . '/add', $newComponent);
        $newCount = DB::table('components')->whereTypeId($type->id)->count();

        $this->assertResponseStatus(302);
        $this->assertSessionHas('success');
        $this->assertEquals($count + 1, $newCount);

        DB::table('components')->whereName($newComponent['name'])->delete();
    }

    /**
     * @covers ComponentController::postAdd
     */
    public function testPostAddFailsValidator()
    {
        $newComponent = [
            'name'                => null,
            'parent_component_id' => null,
            'reporting'           => null
        ];
        $type = Type::orderBy(DB::Raw('RAND()'))->first(); // should work on any type.
        $count = DB::table('components')->whereTypeId($type->id)->count();
        $this->call('POST', '/home/component/' . $type->id . '/add', $newComponent);
        $newCount = DB::table('components')->whereTypeId($type->id)->count();

        $this->assertResponseStatus(302);
        $this->assertSessionHas('error');
        $this->assertEquals($count, $newCount);
    }

    /**
     * @covers ComponentController::postAdd
     */
    public function testPostAddFailsTrigger()
    {
        $type = Type::orderBy(DB::Raw('RAND()'))->first(); // should work on any type.

        $existing = DB::table('components')->whereTypeId($type->id)->first();

        $newComponent = [
            'name'                => $existing->name,
            'parent_component_id' => null,
            'reporting'           => null
        ];

        $count = DB::table('components')->whereTypeId($type->id)->count();
        $this->call('POST', '/home/component/' . $type->id . '/add', $newComponent);
        $newCount = DB::table('components')->whereTypeId($type->id)->count();

        $this->assertResponseStatus(302);
        $this->assertSessionHas('error');
        $this->assertEquals($count, $newCount);
    }

    /**
     * @covers ComponentController::edit
     */
    public function testEdit()
    {
        // find a component to edit:
        $component = DB::table('components')->first();
        $type = Type::find($component->type_id);
        $response = $this->action('GET', 'ComponentController@edit', $component->id);
        $view = $response->original;

        $this->assertResponseOk();
        $this->assertSessionHas('previous');
        $this->assertEquals('Edit ' . $type->type . ' "' . $component->name . '"', $view['title']);
        $this->assertEquals($component->name, $view['component']->name);
        $this->assertEquals($component->name, $view['prefilled']['name']);
    }

    /**
     * @covers ComponentController::edit
     */
    public function testEditWithOldInput()
    {
        // find a component to edit:
        $component = DB::table('components')->first();
        $type = Type::find($component->type_id);

        $oldData = [
            'name' => 'Old input (edited component)',
        ];
        $this->session(['_old_input' => $oldData]);

        $response = $this->action('GET', 'ComponentController@edit', $component->id);
        $view = $response->original;

        $this->assertResponseOk();
        $this->assertEquals('Edit ' . $type->type . ' "' . $component->name . '"', $view['title']);
        $this->assertEquals($component->name, $view['component']->name);
        $this->assertEquals($oldData['name'], $view['prefilled']['name']);
    }

    /**
     * @covers ComponentController::postEdit
     */
    public function testPostEdit()
    {
        // find account to edit.
        $component = DB::table('components')->first();
        $originalName = $component->name;

        $newData = [
            'name'      => 'New Component Name #' . rand(1000, 9999),
            'reporting' => $component->reporting
        ];

        // this should update the component.
        $this->call('POST', '/home/component/' . $component->id . '/edit/', $newData);

        $this->assertSessionHas('success');
        $this->assertResponseStatus(302);
        $newComponent = DB::table('components')->find($component->id);

        // new component name should match
        $this->assertEquals($newData['name'], $newComponent->name);

        // restore component again:
        DB::table('components')->whereId($component->id)->update(['name' => $originalName]);
    }

    /**
     * @covers ComponentController::postEdit
     */
    public function testPostEditFailsValidator()
    {
        // find account to edit.
        $component = DB::table('components')->first();

        $newData = [
            'name'      => null,
            'reporting' => $component->reporting
        ];

        // this should update the component.
        $this->call('POST', '/home/component/' . $component->id . '/edit/', $newData);

        $this->assertSessionHas('error');
        $this->assertResponseStatus(302);
        $newComponent = DB::table('components')->find($component->id);

        $this->assertEquals($newComponent->name, $component->name);
    }

    /**
     * @covers ComponentController::postEdit
     */
    public function testPostEditFailsTrigger()
    {
        // find account to edit.
        $component = DB::table('components')->first();
        $otherComponent = DB::table('components')->where('id', '!=', $component->id)->first();

        $newData = [
            'name'      => $otherComponent->name,
            'reporting' => $component->reporting
        ];

        // this should update the component.
        $this->call('POST', '/home/component/' . $component->id . '/edit/', $newData);

        $this->assertSessionHas('error');
        $this->assertResponseStatus(302);
        $newComponent = DB::table('components')->find($component->id);

        $this->assertEquals($newComponent->name, $component->name);

    }

    /**
     * @covers ComponentController::delete
     * @todo   implement this.
     */
    public function testDelete()
    {
        // find an account to delete:
        $component = DB::table('components')->first();
        $type = Type::find($component->type_id);
        $response = $this->action('GET', 'ComponentController@delete', $component->id);
        $view = $response->original;

        $this->assertResponseOk();
        $this->assertSessionHas('previous');
        $this->assertEquals('Delete ' . $type->type . ' "' . $component->name . '"', $view['title']);
        $this->assertEquals($component->name, $view['component']->name);
    }

    /**
     * @covers ComponentController::postDelete
     */
    public function testPostDelete()
    {
        // create component, delete it.
        $user = User::whereUsername('admin')->first();
        $type = Type::orderBy(DB::Raw('RAND()'))->first(); // should work on any type.
        $toDelete = Component::create(
            [
                'user_id'             => $user->id,
                'type_id'             => $type->id,
                'parent_component_id' => null,
                'name'                => 'New component (to be deleted)',
                'reporting'           => 0,
            ]
        );
        $count = Component::count();

        // this should delete the account.
        $this->call('POST', '/home/component/' . $toDelete->id . '/delete');
        $newCount = Component::count();

        $this->assertSessionHas('success');
        $this->assertResponseStatus(302);
        $this->assertEquals($count - 1, $newCount);
    }

    /**
     * @covers ComponentController::overview
     */
    public function testOverview()
    {
        // get an account:
        $component = Component::first();
        $type = Type::where('id', $component->type_id)->first();

        // this should get the overview
        $response = $this->call('GET', '/home/component/' . $component->id . '/overview/');
        $view = $response->original;

        // count for $months array should match
        // $start is based on the oldest account.
        $account = Account::orderBy('openingbalancedate', 'ASC')->first();
        $start = $account->openingbalancedate;
        $start->firstOfMonth();
        $diff = $start->diffInMonths(new Carbon) + 1;
        $this->assertCount($diff, $view['months']);

        $this->assertResponseOk();
        $this->assertEquals('Overview for ' . $type->type . ' "' . $component->name . '"', $view['title']);
    }

    /**
     * @covers ComponentController::overviewByMonth
     */
    public function testOverviewByMonth()
    {
        // get an account:
        $component = Component::first();
        $type = Type::where('id', $component->type_id)->first();
        $date = new Carbon;

        // this should get the overview
        $response = $this->call('GET', '/home/component/' . $component->id . '/overview/' . $date->format('Y/m'));
        $view = $response->original;

        // will not count the mutations (it's hard)
        // so we avoid doing that until we must test ComponentHelper::transactionsWithoutComponent


        $this->assertResponseOk();
        $this->assertEquals(
            'Overview for ' . $type->type . ' "' . $component->name . '" in ' . $date->format('F Y'), $view['title']
        );
    }

    /**
     * @covers ComponentController::typeahead
     */
    public function testTypeahead()
    {
        // get a type:
        $type = Type::orderBy(DB::Raw('RAND()'))->first(); // should work on any type.

        // this should get the overview
        $response = $this->call('GET', '/home/type/' . $type->id . '/typeahead');
        $jsonContent = $response->getContent();
        $json = json_decode($jsonContent);

        $count = DB::table('components')->where('type_id',$type->id)->count();
        $this->assertResponseOk();
        $this->assertCount($count,$json);
    }

    /**
     * @covers ComponentController::renderIcon
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testRenderIcon()
    {
        $component = Component::first();
        $this->action('GET', 'ComponentController@renderIcon',$component->id);

    }

} 