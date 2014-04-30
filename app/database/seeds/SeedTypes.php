<?php

class SeedTypes extends Seeder
{
    public function run()
    {
        $user = User::first();

        Type::create(
            [
                'beneficiary'
            ]
        );
        Type::create(
            [
                'category'
            ]
        );
        Type::create(
            [
                'budget'
            ]
        );
    }

}
