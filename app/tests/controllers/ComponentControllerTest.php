<?php

/**
 * Class ComponentControllerTest
 */
class ComponentControllerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $user = User::where('username', 'test')->first();
        $this->be($user);

    }

    public function testShowIndex()
    {
        Route::enableFilters();
        $response = $this->call('GET', 'home/budget');
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals('All budgets', $view['title']);

        $count = Auth::user()->components()->where('type', 'budget')->whereNull('parent_component_id')->count();
        $this->assertEquals($count, count($view['objects']));

        Route::disableFilters();

    }

    public function testTypeahead()
    {
        $response = $this->call(
            'GET', 'home/budget/typeahead'
        );
        $this->assertResponseStatus(200);
        $this->assertNotNull($response);

        $jsonResponse = $this->client->getResponse()->getContent();
        $responseData = json_decode($jsonResponse, true);
        $count = Auth::user()->components()->where('type', 'budget')->count();
        $this->assertCount($count, $responseData);
    }

    public function testShowEmpty()
    {
        Route::enableFilters();
        $response = $this->call('GET', 'home/budget/empty');
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals('Transactions without a budget', $view['title']);
        $transactions = Auth::user()->transactions()->get();
        $count = 0;
        foreach ($transactions as $t) {
            if (is_null($t->budget)) {
                $count++;
            }
        }
        $this->assertCount($count, $view['transactions']);
        Route::disableFilters();
    }

    public function testShowEmptyByMonth()
    {
        Route::enableFilters();
        $response = $this->call('GET', 'home/budget/empty/' . date('Y/m'));
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals('Transactions without a budget', $view['title']);
        $transactions = Auth::user()->transactions()->inMonth(new \Carbon\Carbon)->get();
        $count = 0;
        foreach ($transactions as $t) {
            if (is_null($t->budget)) {
                $count++;
            }
        }
        $this->assertCount($count, $view['transactions']);
        Route::disableFilters();
    }

    public function testAdd()
    {
        Route::enableFilters();
        $response = $this->call('GET', 'home/budget/add');
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals('Add new budget', $view['title']);
        $this->assertSessionHas('previous');
        // +1 = no parent
        $count = Auth::user()->components()->where('type', 'budget')->whereNull('parent_component_id')->count() + 1;
        $this->assertCount($count, $view['parents']);
        Route::disableFilters();

    }

    public function testEmptyPostAdd()
    {
        $count = Auth::user()->components()->count();
        $this->call('POST', 'home/budget/add');
        $newCount = Auth::user()->components()->count();
        $this->assertResponseStatus(302);
        $this->assertEquals($count, $newCount);
        $this->assertSessionHas('error');
        $this->assertRedirectedToRoute('addbudget');
        $this->assertHasOldInput();
    }

    public function testPostAdd()
    {
        $count = Auth::user()->components()->count();
        $data = ['name'      => 'InTestComponent', 'type' => 'budget',
                 'reporting' => 1, 'parent_component_id' => null];

        $this->call('POST', 'home/budget/add', $data);
        $newCount = Auth::user()->components()->count();
        $this->assertEquals($count + 1, $newCount);
        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('index');
        $this->assertSessionHas('success');
    }

    public function testPostWithParentAdd()
    {
        $count = Auth::user()->components()->count();
        $component = Auth::user()->components()->whereNull('parent_component_id')->first();
        $data = ['name'      => 'Bla.', 'type' => 'budget',
                 'reporting' => 1, 'parent_component_id' => $component->id];

        $this->call('POST', 'home/budget/add', $data);
        $newCount = Auth::user()->components()->count();
        $this->assertEquals($count + 1, $newCount);
        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('index');
        $this->assertSessionHas('success');
    }

    public function testPostTriggerError()
    {
        $count = Auth::user()->components()->count();
        $component = Auth::user()->components()->whereNotNull('parent_component_id')->first();
        $data = ['name'      => 'Bla.', 'type' => 'budget',
                 'reporting' => 1, 'parent_component_id' => $component->id];

        $this->call('POST', 'home/budget/add', $data);
        $newCount = Auth::user()->components()->count();
        $this->assertEquals($count, $newCount);
        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('addbudget');
        $this->assertSessionHas('error');
    }

    public function testEdit()
    {
        Route::enableFilters();
        $component = Auth::user()->components()->first();
        $response = $this->call(
            'GET', 'home/budget/' . $component->id . '/edit'
        );
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals('Edit budget ' . $component->name, $view['title']);
        $this->assertSessionHas('previous');
        $this->assertCount(1, $view['parents']);
        Route::disableFilters();

    }

    public function testWithParentEdit()
    {
        Route::enableFilters();
        $component = Auth::user()->components()->whereNotNull('parent_component_id')->first();
        $response = $this->call(
            'GET', 'home/budget/' . $component->id . '/edit'
        );
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals('Edit budget ' . $component->name, $view['title']);
        $this->assertSessionHas('previous');
        $count = Auth::user()->components()->whereNull('parent_component_id')->where('id', '!=', $component->id)->where(
                'type', 'budget'
            )->count() + 1;

        $this->assertCount($count, $view['parents']);
        Route::disableFilters();
    }

    public function testPostEdit()
    {
        $component = Auth::user()->components()->whereNull('parent_component_id')->orderBy('ID', 'DESC')->first();
        $data = ['name' => 'EditedComponent'];
        $this->call('POST', 'home/budget/' . $component->id . '/edit', $data);
        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('index');
        $this->assertSessionHas('success');

    }

    public function testPostEditWithParent()
    {
        $component = Auth::user()->components()->whereNotNull('parent_component_id')->orderBy('ID', 'DESC')->first();
        $data = ['name'                => 'EditedParentComponent',
                 'parent_component_id' => $component->parent_component_id];
        $this->call('POST', 'home/budget/' . $component->id . '/edit', $data);
        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('index');
        $this->assertSessionHas('success');
    }

    public function testPostEditInvalid()
    {
        $component = Auth::user()->components()->first();
        $data = ['name' => null];
        $this->call('POST', 'home/budget/' . $component->id . '/edit', $data);
        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('editbudget', $component->id);
        $this->assertSessionHas('error');
    }

    public function testPostEditDouble()
    {
        $component = Auth::user()->components()->first();
        $data = ['name' => 'EditedParentComponent'];
        $this->call('POST', 'home/budget/' . $component->id . '/edit', $data);
        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('editbudget', $component->id);
        $this->assertSessionHas('error');
    }

    public function testDelete()
    {
        $component = Auth::user()->components()->first();
        $response = $this->call(
            'GET', 'home/budget/' . $component->id . '/delete'
        );
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertSessionHas('previous');
        $this->assertEquals(
            $view['title'], 'Delete budget ' . $component->name
        );
        $this->assertEquals($view['object']->name, $component->name);

    }

    public function testPostDelete()
    {
        $count = Auth::user()->components()->count();
        $component = Auth::user()->components()->orderBy('ID', 'DESC')->whereNull('parent_component_id')->first();
        $this->call('POST', 'home/budget/' . $component->id . '/delete');
        $this->assertResponseStatus(302);
        $newCount = Auth::user()->components()->count();
        $this->assertEquals($count - 1, $newCount);
        $this->assertRedirectedToRoute('index');
        $this->assertSessionHas('success');
    }

    public function testPostParentDelete()
    {
        $count = Auth::user()->components()->count();
        $component = Auth::user()->components()->orderBy('ID', 'DESC')->whereNotNull('parent_component_id')->first();
        $this->call('POST', 'home/budget/' . $component->id . '/delete');
        $this->assertResponseStatus(302);
        $newCount = Auth::user()->components()->count();
        $this->assertEquals($count - 1, $newCount);
        $this->assertRedirectedToRoute('index');
        $this->assertSessionHas('success');
    }

    public function testShowOverview()
    {
        $component = Auth::user()->components()->first();
        $response = $this->call(
            'GET', 'home/budget/' . $component->id . '/overview'
        );
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals(
            $view['title'], 'Overview for budget "' . $component->name . '"'
        );
        $count = $component->transactions()->count();
        $this->assertEquals($view['component']->name, $component->name);
        $this->assertCount($count, $view['transactions']);
        $this->assertEquals('transactions', $view['display']);

    }

    public function testShowOverviewByMonth()
    {
        $component = Auth::user()->components()->first();
        $response = $this->call(
            'GET', 'home/budget/' . $component->id . '/overview/' . date('Y/m')
        );
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals(
            $view['title'],
            'Overview for budget "' . $component->name . '" in ' . date('F Y')
        );
        $this->assertEquals($view['component']->name, $component->name);
        $this->assertCount(0, $view['transactions']);
        $this->assertEquals('transactions', $view['display']);
    }

    public function testShowOverviewByMonthMontly()
    {
        $component = Auth::user()->components()->first();
        $response = $this->call(
            'GET', 'home/budget/' . $component->id . '/overview?monthly=true'
        );
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals(
            $view['title'], 'Overview for budget "' . $component->name . '"'
        );
        $this->assertEquals($view['component']->name, $component->name);
        // TODO remove hard coded count!
//        $this->assertCount(16, $view['transactions']);
        $this->assertEquals('months', $view['display']);
    }


}