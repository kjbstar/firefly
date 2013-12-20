<?php
use Carbon\Carbon as Carbon;

Breadcrumbs::register(
    'home', function ($breadcrumbs) {
        $breadcrumbs->push('Home', route('home'));
    }
);


// add, edit, delete
// Transfer, Transaction, Account, beneficiary (fake), budget (fake),
//category (fake)

$objects = ['account', 'beneficiary', 'transaction', 'budget', 'transfer',
            'category'];


foreach ($objects as $object) {

    // route to list of everything:
    Breadcrumbs::register(
        Str::plural($object), function ($breadcrumbs) use ($object) {
            $breadcrumbs->parent('home');
            $breadcrumbs->push(
                'All ' . Str::plural($object), route(Str::plural($object))
            );
        }
    );

    // route to add stuff:
    $addCrumb = 'add' . $object;
    $addTitle = 'Add a new ' . $object;
    Breadcrumbs::register(
        $addCrumb, function ($breadcrumbs) use ($addCrumb, $addTitle, $object) {
            $breadcrumbs->parent(Str::plural($object));
            $breadcrumbs->push($addTitle, route($addCrumb));
        }
    );

    // route to edit stuff:
    Breadcrumbs::register(
        'edit' . $object, function (
            $breadcrumbs, $obj
        ) use ($object) {
            $breadcrumbs->parent(Str::plural($object));
            if (isset($obj->name)) {
                $name = $obj->name;
            } else {
                if (isset($obj->description)) {
                    $name = $obj->description;

                } else {
                    $name = 'XXXXX';
                }
            }

            $breadcrumbs->push(
                'Edit ' . $name, route(
                    'edit' . $object, $obj->id
                )
            );
        }
    );

    // route for overview of stuff:
    Breadcrumbs::register(
        $object, function (
            $breadcrumbs, $obj, Carbon $date = null
        ) use ($object) {
            $breadcrumbs->parent(Str::plural($object));

            if (isset($obj->name)) {
                $name = $obj->name;
            } else {
                if (isset($obj->description)) {
                    $name = $obj->description;

                } else {
                    $name = 'XXXXX';
                }
            }

            $breadcrumbs->push(
                $name, route(
                    $object.'overview', $obj->id
                )
            );
            if ($date) {
                $breadcrumbs->push(
                    $date->format('F Y'), route(
                        $object.'overview', $obj->id, $date->format('Y'),
                        $date->format('m')
                    )
                );
            }
        }
    );
}
