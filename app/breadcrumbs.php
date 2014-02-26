<?php
use Carbon\Carbon as Carbon;

Breadcrumbs::register(
    'home', function ($breadcrumbs) {
        $breadcrumbs->push('Home', route('home'));
    }
);
Breadcrumbs::register(
    'reports', function ($breadcrumbs) {
        $breadcrumbs->parent('home');
        $breadcrumbs->push('Reports', route('reports'));
    }
);
/**
 * PIGGY BANKS:
 */
Breadcrumbs::register('piggy',function($breadcrumbs) {
        $breadcrumbs->parent('home');
        $breadcrumbs->push('Piggy banks', route('piggy'));
    });

Breadcrumbs::register('piggyselect',function($breadcrumbs) {
        $breadcrumbs->parent('piggy');
        $breadcrumbs->push('Select account', route('piggyselect'));
    });

Breadcrumbs::register('addpiggybank',function($breadcrumbs) {
        $breadcrumbs->parent('piggy');
        $breadcrumbs->push('Add new piggy bank', route('addpiggybank'));
    });

/**
 * PREDICTABLES:
 */


// route to edit stuff:
Breadcrumbs::register(
    'editpiggy', function ($breadcrumbs, Piggybank $pig) {
        $breadcrumbs->parent('piggy');
        $breadcrumbs->push('Edit ' .$pig->name, route('editpiggy', $pig->id));
    }
);


Breadcrumbs::register(
    'report', function ($breadcrumbs,$year) {
        $breadcrumbs->parent('reports');

        $breadcrumbs->push($year, route('yearreport',$year));
    }
);

Breadcrumbs::register(

    'settings', function ($breadcrumbs) {
        $breadcrumbs->parent('home');
        $breadcrumbs->push('Settings', route('settings'));
    }
);

Breadcrumbs::register(

    'allowances', function ($breadcrumbs) {
        $breadcrumbs->parent('home');
        $breadcrumbs->push('Allowances', route('allowances'));
    }
);
Breadcrumbs::register(

    'addallowance', function ($breadcrumbs) {
        $breadcrumbs->parent('allowances');
        $breadcrumbs->push('Add allowance', route('addallowance'));
    }
);


// add, edit, delete
// Transfer, Transaction, Account, beneficiary (fake), budget (fake),
//category (fake)

$objects = ['account', 'beneficiary', 'transaction', 'budget', 'transfer',
            'category','predictable'];


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

    // route for empty object
    Breadcrumbs::register(
        'empty'.$object, function ($breadcrumbs,$date) use ($object) {
            $breadcrumbs->parent('home');
            $breadcrumbs->push(
                'Transactions without a ' . $object,
                route('empty'.$object)
            );
            if(!is_null($date)) {
                $breadcrumbs->push(
                    $date->format('F Y'),
                    route('empty'.$object,[$date->format('Y'),
                                          $date->format('m')])
                );
            }
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
