<?php

class SeedTypes extends Seeder
{
    public function run()
    {
        Eloquent::unguard();
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
