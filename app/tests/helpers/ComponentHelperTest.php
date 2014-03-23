<?php


class ComponentHelperTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $user = User::where('username', 'test')->first();
        $this->be($user);
        if(!defined('OBJ')) {
            define('OBJ','budget');
        }
    }

    public function testGenerateOverviewOfMonths()
    {

        $start = Toolkit::getEarliestEvent();
        $now = new \Carbon\Carbon();
        $now->addMonth();
        $diff = $now->diffInMonths($start) + 1;
        $component = Auth::user()->components()->first();
        $list = ComponentHelper::generateOverviewOfMonths($component);
        $this->assertCount($diff,$list);
    }

    public function testGenerateTransactionListByMonth()
    {
        $component = Auth::user()->components()->first();
        $date = new \Carbon\Carbon();
        $date->subMonths(3);
        // count in month:
        $count = $component->transactions()->inMonth($date)->count();
        $list = ComponentHelper::generateTransactionListByMonth($component,$date);
        $this->assertCount($count,$list);
    }

    public function testTransactionsWithoutComponent()
    {
        $list = ComponentHelper::transactionsWithoutComponent('budget');

    }
    public function testTransactionsWithoutComponentWithDate()
    {
        // TODO more tests.
        $date = new \Carbon\Carbon();
        $date->subMonths(3);

        $list = ComponentHelper::transactionsWithoutComponent('budget',$date);
    }

    public function testGetParentList()
    {
        // possible parents for (new) components of type 'budget'.
        $list = ComponentHelper::getParentList('budget');
        $count = Auth::user()->components()->where('type','budget')->count();
        //$this->assertCount($count+1,$list);
    }
    public function testGetParentListNull()
    {
        $component =  Auth::user()->components()->where('type','budget')->first();
        $list = ComponentHelper::getParentList('budget',$component);

        $raw = Auth::user()->components()->where('type','budget')->
            whereNull('parent_component_id')->
            where('id','!=',$component->id)->count();
        $this->assertCount($raw+1,$list);
    }

} 