<?php

class SeedTypes extends Seeder
{
    public function run()
    {
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
