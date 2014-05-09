<?php


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
        $count = DB::table('components')->whereNull('parent_component_id')->where('type_id',$type->id)->count();
        $this->assertCount($count, $view['components']);
        $this->assertEquals($type->type,$view['type']->type);

    }

    /**
     * @covers ComponentController::noComponent
     * @todo   implement this.
     */
    public function testNoComponent()
    {
            
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers ComponentController::add
     * @todo   implement this.
     */
    public function testAdd()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers ComponentController::postAdd
     * @todo   implement this.
     */
    public function testPostAdd()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers ComponentController::edit
     * @todo   implement this.
     */
    public function testEdit()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers ComponentController::postEdit
     * @todo   implement this.
     */
    public function testPostEdit()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers ComponentController::delete
     * @todo   implement this.
     */
    public function testDelete()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers ComponentController::postDelete
     * @todo   implement this.
     */
    public function testPostDelete()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers ComponentController::overview
     * @todo   implement this.
     */
    public function testOverview()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers ComponentController::overviewByMonth
     * @todo   implement this.
     */
    public function testOverviewByMonth()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers ComponentController::typeahead
     * @todo   implement this.
     */
    public function testTypeahead()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers ComponentController::renderIcon
     * @todo   implement this.
     */
    public function testRenderIcon()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

} 