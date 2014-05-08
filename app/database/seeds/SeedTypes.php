<?php

class SeedTypes extends Seeder
{
    public function run()
    {
        Eloquent::unguard();

        foreach(Type::get() as $t) {
            $t->delete();
        }
        \Type::create(
            [
                'type' => 'beneficiary'
            ]
        );
        \Type::create(
            [
                'type' => 'category'
            ]
        );
        \Type::create(
            [
                'type' => 'budget'
            ]
        );
    }

}
