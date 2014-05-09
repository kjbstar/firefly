<?php


class SeedComponents extends Seeder
{
    public function run()
    {
        Eloquent::unguard();
        $user = User::whereUsername('admin')->first();

        // create some components.
        // four of each type.
        // fourth one is child of first one:

        $beneficiary = Type::where('type', 'beneficiary')->first();
        $category = Type::where('type', 'category')->first();
        $budget = Type::where('type', 'budget')->first();

        $entries = [
            $beneficiary->id => ['Shell', 'Albert Heijn', 'GAMMA', 'Shell Amsterdam'],
            $category->id    => ['House', 'Food and drinks', 'Going out', 'Maintenance'],
            $budget->id      => ['Daily expenses', 'Bills', 'Everything else', 'Daily groceries']
        ];

        foreach($entries as $typeID => $row) {
            $parentID = null; // save for later.
            foreach($row as $index => $component) {
                // create the current component:
                $id = Component::insertGetId(
                    [
                        'user_id' => $user->id,
                        'type_id' => $typeID,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'parent_component_id' => $index == 3 ? $parentID : null,
                        'name' => $component,
                        'reporting' => 0
                    ]
                );
                if($index == 0) {
                    $parentID = $id;
                }
                unset($id);

            }
        }


    }

} 