<?php

class TestSeedComponents extends Seeder
{
    public function run()
    {
        DB::table('components')->delete();
        $user = User::first();

        $types = ['budget', 'beneficiary', 'category'];
        for ($i = 0; $i < 3; $i++) {
            $type = $types[$i];
            for ($j = 0; $j < 2; $j++) {
                $p = null;
                if($j == 1) {
                    /** @var $c Component */
                    $p = $c->id;
                }
                $c = Component::create(
                    ['user_id' => $user->id, 'name' => 'Test'.ucfirst($type).' #'.($j+1),
                     'type'    => $type, 'reporting' => 1,'parent_component_id' => $p]
                );
            }
        }

    }
} 