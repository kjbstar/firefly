<?php

/**
 * Class PredictableControllerTest
 */
class PredictableControllerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $user = User::where('username', 'test')->first();
        $this->be($user);
    }

    private $_amount = 123.45;

    public function testIndex()
    {
        $response = $this->call('GET', 'home/predictable');
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals('Predictables', $view['title']);
        $predictable = Predictable::count();
        $this->assertCount($predictable, $view['predictables']);
    }

    public function testOverview()
    {
        $predictable = Predictable::first();
        $response = $this->call(
            'GET', 'home/predictable/' . $predictable->id . '/overview'
        );
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals('Overview for ' . $predictable->description, $view['title']);
        $this->assertEquals($predictable->id, $view['predictable']->id);


    }

    public function testAdd()
    {
        $response = $this->call('GET', 'home/predictable/add');
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals('Add a predictable', $view['title']);
        // +1 is no parent
        $bud = Auth::user()->components()->where('type', 'budget')->whereNull('parent_component_id')->count() + 1;
        $cat = Auth::user()->components()->where('type', 'category')->whereNull('parent_component_id')->count() + 1;
        $ben = Auth::user()->components()->where('type', 'beneficiary')->whereNull('parent_component_id')->count() + 1;

        $this->assertCount(3, $view['components']);
        $this->assertCount($bud, $view['components']['budget']);
        $this->assertCount($cat, $view['components']['category']);
        $this->assertCount($ben, $view['components']['beneficiary']);

    }

    public function testAddWithTransaction()
    {
        $transaction = Transaction::first();
        $response = $this->call('GET', 'home/predictable/add/' . $transaction->id);
        $view = $response->original;
        $this->assertResponseStatus(200);
        // TODO match prefilled content
        $this->assertEquals('Add a predictable based on "' . $transaction->description . '"', $view['title']);
        $this->assertEquals($transaction->description, $view['prefilled']['description']);

        $bud = Auth::user()->components()->where('type', 'budget')->whereNull('parent_component_id')->count() + 1;
        $cat = Auth::user()->components()->where('type', 'category')->whereNull('parent_component_id')->count() + 1;
        $ben = Auth::user()->components()->where('type', 'beneficiary')->whereNull('parent_component_id')->count() + 1;

        $this->assertCount(3, $view['components']);
        $this->assertCount($bud, $view['components']['budget']);
        $this->assertCount($cat, $view['components']['category']);
        $this->assertCount($ben, $view['components']['beneficiary']);
    }

    public function testAddWithOldInput()
    {
        $this->session(['_old_input' => ['description' => 'Test', 'amount' => 100]]);
        $response = $this->call('GET', 'home/predictable/add/');
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals('Add a predictable', $view['title']);
        $this->assertEquals('Test', $view['prefilled']['description']);
        $this->assertEquals(100, $view['prefilled']['amount']);
        $bud = Auth::user()->components()->where('type', 'budget')->whereNull('parent_component_id')->count() + 1;
        $cat = Auth::user()->components()->where('type', 'category')->whereNull('parent_component_id')->count() + 1;
        $ben = Auth::user()->components()->where('type', 'beneficiary')->whereNull('parent_component_id')->count() + 1;
        $this->assertCount(3, $view['components']);
        $this->assertCount($bud, $view['components']['budget']);
        $this->assertCount($cat, $view['components']['category']);
        $this->assertCount($ben, $view['components']['beneficiary']);
    }

    public function testPostAdd()
    {
        $count = Auth::user()->predictables()->count();
        $beneficiary = Component::where('type', 'beneficiary')->first();
        $category = Component::where('type', 'category')->first();
        $budget = Component::where('type', 'budget')->first();

        $data = ['description'    => 'TestPredictableFilled', 'dom' => 1, 'pct' => 12,
                 'inactive'       => 0, 'amount' => $this->_amount,
                 'beneficiary_id' => $beneficiary->id, 'budget_id' => $budget->id, 'category_id' => $category->id
        ];
        $this->call('POST', 'home/predictable/add', $data);
        $newCount = Auth::user()->predictables()->count();
        $this->assertResponseStatus(302);
        $this->assertEquals($count + 1, $newCount);
        $this->assertSessionHas('success');
        $this->assertRedirectedToRoute('index');
    }

    public function testPostAddEmpty()
    {
        $count = Auth::user()->predictables()->count();
        $this->call('POST', 'home/predictable/add');
        $newCount = Auth::user()->predictables()->count();
        $this->assertResponseStatus(302);
        $this->assertEquals($count, $newCount);
        $this->assertSessionHas('error');
        $this->assertRedirectedToRoute('addpredictable');
    }

    public function testPostAddInvalid()
    {
        $count = Auth::user()->predictables()->count();
        $data = ['description' => null];
        $this->call('POST', 'home/predictable/add', $data);
        $newCount = Auth::user()->predictables()->count();
        $this->assertResponseStatus(302);
        $this->assertEquals($count, $newCount);
        $this->assertSessionHas('error');
        $this->assertRedirectedToRoute('addpredictable');

    }

    /**
     * @depends testPostAdd
     */
    public function testPostAddDouble()
    {
        $count = Auth::user()->predictables()->count();
        $data = ['description' => 'TestPredictableFilled', 'dom' => 1, 'pct' => 12,
                 'inactive'    => 0, 'amount' => $this->_amount,
        ];
        $this->call('POST', 'home/predictable/add', $data);
        $newCount = Auth::user()->predictables()->count();
        $this->assertResponseStatus(302);
        $this->assertEquals($count, $newCount);
        $this->assertSessionHas('error');
        $this->assertRedirectedToRoute('addpredictable');

    }

    /**
     * @depends testPostAdd
     */
    public function testEdit()
    {
        $predictable = Predictable::orderBy('id', 'DESC')->where('amount', $this->_amount)->first();
        $response = $this->call('GET', 'home/predictable/' . $predictable->id . '/edit');
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals('Edit predictable "' . $predictable->description . '"', $view['title']);
        $this->assertCount(3, $view['components']);

    }

    public function testPostEdit()
    {

        $beneficiary = Component::where('type', 'beneficiary')->first();
        $category = Component::where('type', 'category')->first();
        $budget = Component::where('type', 'budget')->first();

        $pred = Predictable::orderBy('id', 'DESC')->where('amount', $this->_amount)->first();
        $data = ['description'    => 'TestPredictableFilledEdited', 'dom' => 1, 'pct' => 12,
                 'inactive'       => 0, 'amount' => $this->_amount,
                 'beneficiary_id' => $beneficiary->id, 'budget_id' => $budget->id, 'category_id' => $category->id
        ];
        $this->call('POST', 'home/predictable/' . $pred->id . '/edit', $data);
        $this->assertResponseStatus(302);
        $this->assertSessionHas('success');
        $this->assertRedirectedToRoute('index');
    }

    public function testPostEditInvalid()
    {
        // TODO  errors
        $pred = Predictable::orderBy('id', 'DESC')->first();
        $data = ['description' => null
        ];
        $this->call('POST', 'home/predictable/' . $pred->id . '/edit', $data);
        $this->assertResponseStatus(302);
        $this->assertHasOldInput();
        $this->assertSessionHas('error');
        $this->assertRedirectedToRoute('editpredictable', $pred->id);
    }

    public function testPostEditDouble()
    {
        $pred = Predictable::orderBy('id', 'DESC')->first();

        $beneficiary = Component::where('type', 'beneficiary')->first();
        $category = Component::where('type', 'category')->first();
        $budget = Component::where('type', 'budget')->first();

        $data = ['description'    => 'Rent', 'dom' => 1, 'pct' => 12,
                 'inactive'       => 0, 'amount' => $this->_amount,
                 'beneficiary_id' => $beneficiary->id, 'budget_id' => $budget->id, 'category_id' => $category->id
        ];;
        $this->call('POST', 'home/predictable/' . $pred->id . '/edit', $data);
        $this->assertResponseStatus(302);
        $this->assertHasOldInput();
        $this->assertSessionHas('error');
        $this->assertRedirectedToRoute('editpredictable', $pred->id);
    }

    public function testDelete()
    {
        $pred = Predictable::orderBy('id', 'DESC')->where('amount', $this->_amount)->first();
        $response = $this->call('GET', 'home/predictable/' . $pred->id . '/delete');
        $view = $response->original;
        $this->assertResponseStatus(200);
        $this->assertEquals('Delete predictable ' . $pred->description, $view['title']);

    }

    public function testRescan()
    {
        $pred = Predictable::orderBy('id', 'DESC')->where('amount', $this->_amount)->first();

        $this->call('GET', 'home/predictable/' . $pred->id . '/rescan');
        $this->assertResponseStatus(302);
        $this->assertSessionHas('success');
        $this->assertRedirectedToRoute('predictableoverview', $pred->id);
    }

    public function testRescanAll()
    {
        $pred = Predictable::orderBy('id', 'DESC')->where('amount', $this->_amount)->first();

        $this->call('GET', 'home/predictable/' . $pred->id . '/rescan-all');
        $this->assertResponseStatus(302);
        $this->assertSessionHas('success');
        $this->assertRedirectedToRoute('predictableoverview', $pred->id);
    }

    public function testPostDelete()
    {
        $count = Auth::user()->predictables()->count();
        $pred = Predictable::orderBy('id', 'DESC')->where('amount', $this->_amount)->first();

        $this->call('POST', 'home/predictable/' . $pred->id . '/delete');
        $newCount = Auth::user()->predictables()->count();
        $this->assertResponseStatus(302);
        $this->assertEquals($count - 1, $newCount);
        $this->assertSessionHas('success');
        $this->assertRedirectedToRoute('index');
    }

    public static function tearDownAfterClass() // moet na class, niet na test!
    {
        parent::tearDownAfterClass();
        DB::table('predictables')->where('amount', 123.45)->delete();
    }


}