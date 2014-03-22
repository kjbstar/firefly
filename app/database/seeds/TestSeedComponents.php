<?php

class TestSeedComponents extends Seeder
{
    public function run()
    {
        DB::table('components')->delete();
        $user = User::first();

//        $types = ['budget', 'beneficiary', 'category'];
//        for ($i = 0; $i < 3; $i++) {
//            $type = $types[$i];
//            for ($j = 0; $j < 2; $j++) {
//                $p = null;
//                if($j == 1) {
//                    /** @var $c Component */
//                    $p = $c->id;
//                }
//                $c = Component::create(
//                    ['user_id' => $user->id, 'name' => 'Test'.ucfirst($type).' #'.($j+1),
//                     'type'    => $type, 'reporting' => 1,'parent_component_id' => $p]
//                );
//            }
//        }

        // we create some actual beneficiaries for
        // our 'actual' transactions:
        $arr = [
            ['type' => 'budget', 'name' => 'Groceries', 'parent' => false],
            ['type' => 'budget', 'name' => 'Bills', 'parent' => true],
            ['type' => 'budget', 'name' => 'Everything else', 'parent' => false],
            ['type' => 'beneficiary', 'name' => 'Landlord', 'parent' => false],
            ['type' => 'beneficiary', 'name' => 'InsuranceCompany', 'parent' => false],
            ['type' => 'beneficiary', 'name' => 'InternetCompany', 'parent' => false],
            ['type' => 'beneficiary', 'name' => 'PhoneCompany', 'parent' => false],
            ['type' => 'beneficiary', 'name' => 'PowerCompany', 'parent' => false],
            ['type' => 'beneficiary', 'name' => 'CigaretteShop', 'parent' => false],
            ['type' => 'beneficiary', 'name' => 'Supermarket', 'parent' => true],
            ['type' => 'category', 'name' => 'House', 'parent' => false],
            ['type' => 'category', 'name' => 'Phone', 'parent' => false],
            ['type' => 'category', 'name' => 'Insurance', 'parent' => true],
            ['type' => 'category', 'name' => 'Daily groceries', 'parent' => false],
            ['type' => 'category', 'name' => 'Cigarettes', 'parent' => false],

        ];
        foreach ($arr as $index => $comp) {
            $c = Component::create(
                ['user_id' => $user->id, 'name' => $comp['name'],
                 'type'    => $comp['type'], 'reporting' => 1, 'parent_component_id' => null]
            );
            if($comp['parent'] === true) {
                Component::create(
                    ['user_id' => $user->id, 'name' => $comp['name'].' Child #1',
                     'type'    => $comp['type'], 'reporting' => 1, 'parent_component_id' => $c->id]
                );
                Component::create(
                    ['user_id' => $user->id, 'name' => $comp['name'].' Child #2',
                     'type'    => $comp['type'], 'reporting' => 1, 'parent_component_id' => $c->id]
                );
            }
        }


    }
} 