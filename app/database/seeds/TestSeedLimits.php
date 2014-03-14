<?php

class TestSeedLimits extends Seeder
{
    public function run()
    {
        DB::table('limits')->delete();
        // three limits for each one that has no parent.

        $components = Component::whereNull('parent_component_id')->get();
        foreach($components as $c) {
            // limit always in the current month.
            Limit::create(
                ['component_id' => $c->id, 'amount' => 100,
                 'date'         => date('Y-m-d')]
            );
        }
    }
}