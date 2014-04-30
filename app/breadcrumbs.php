<?php
use Carbon\Carbon as Carbon;

Breadcrumbs::register(
    'home', function ($breadcrumbs) {
        $breadcrumbs->push('Home', route('home'));
    }
);
/**
 * PIGGY BANKS:
 */
Breadcrumbs::register(
    'piggy', function ($breadcrumbs) {
        $breadcrumbs->parent('home');
        $breadcrumbs->push('Piggy banks', route('piggy'));
    }
);

Breadcrumbs::register(
    'piggyselect', function ($breadcrumbs) {
        $breadcrumbs->parent('piggy');
        $breadcrumbs->push('Select account', route('piggyselect'));
    }
);

Breadcrumbs::register(
    'addpiggybank', function ($breadcrumbs) {
        $breadcrumbs->parent('piggy');
        $breadcrumbs->push('Add new piggy bank', route('addpiggybank'));
    }
);

// route to edit stuff:
Breadcrumbs::register(
    'editpiggy', function ($breadcrumbs, Piggybank $pig) {
        $breadcrumbs->parent('piggy');
        $breadcrumbs->push('Edit ' . $pig->name, route('editpiggy', $pig->id));
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

/**
 * REPORTS
 */
Breadcrumbs::register(
    'reports', function ($breadcrumbs) {
        $breadcrumbs->parent('home');
        $breadcrumbs->push('Reports', route('reports'));
    }
);

Breadcrumbs::register(
    'report_year', function ($breadcrumbs, $year) {
        $breadcrumbs->parent('reports');
        $breadcrumbs->push('Report for ' . $year, route('yearreport', $year));
    }
);

Breadcrumbs::register(
    'report_month', function ($breadcrumbs, Carbon $date) {
        $breadcrumbs->parent('report_year', $date->format('Y'));
        $breadcrumbs->push(
            'Report for ' . $date->format('F, Y'), route('monthreport', [$date->format('Y'), $date->format('m')])
        );
    }
);
Breadcrumbs::register(
    'report_compare_month', function ($breadcrumbs, Carbon $one, Carbon $two) {
        $breadcrumbs->parent('reports');
        $breadcrumbs->push('Comparing ' . $one->format('F Y') . ' with ' . $two->format('F Y'));
    }
);
Breadcrumbs::register(
    'report_compare_year', function ($breadcrumbs, Carbon $one, Carbon $two) {
        $breadcrumbs->parent('reports');
        $breadcrumbs->push('Comparing ' . $one->format('Y') . ' with ' . $two->format('Y'));
    }
);

/**
 * ALL COMPONENT BREAD CRUMBS
 */
Breadcrumbs::register(
    'components', function ($breadcrumbs, Type $type) {
        $breadcrumbs->parent('home');
        $breadcrumbs->push(
            'All ' . Str::plural($type->type), route('components', $type->id)
        );
    }
);

Breadcrumbs::register(
    'addcomponent', function ($breadcrumbs, Type $type) {
        $breadcrumbs->parent('components', $type);
        $breadcrumbs->push(
            'Add ' . $type->type, route('addcomponent', $type->id)
        );
    }
);

Breadcrumbs::register(
    'componentoverview', function ($breadcrumbs, Component $component) {
        $breadcrumbs->parent('components', $component->type);
        $breadcrumbs->push(
            'Overview for '.$component->type->type.' "'  .ucfirst($component->name) . '"', route('componentoverview', $component->id)
        );
    }
);

Breadcrumbs::register(
    'componentoverviewmonth', function ($breadcrumbs, Component $component, Carbon $date) {
        $breadcrumbs->parent('componentoverview', $component);
        $breadcrumbs->push(
            $date->format('F Y'),
            route('componentoverview', [$component->id, $date->format('Y'), $date->format('m')])
        );
    }
);

Breadcrumbs::register(
    'editcomponent', function ($breadcrumbs, Component $component) {
        $breadcrumbs->parent('components', $component->type);
        $breadcrumbs->push(
            'Edit ' . $component->type->type . ' "' . $component->name . '"', route('editcomponent', $component->id)
        );
    }
);
Breadcrumbs::register(
    'empty' , function ($breadcrumbs,$type, $date) {
        $breadcrumbs->parent('home');
        $breadcrumbs->push(
            'Transactions without a ' . $type->type, route('empty',$type->id)
        );
        if (!is_null($date)) {
            $breadcrumbs->push(
                $date->format('F Y'), route(
                    'empty',
                    [$type->id, $date->format('Y'), $date->format('m')]
                )
            );
        }
    }
);

// profile:
Breadcrumbs::register(
    'profile', function ($breadcrumbs) {
        $breadcrumbs->parent('home');
        $breadcrumbs->push('Profile', route('profile'));
    }
);

Breadcrumbs::register(
    'change-password', function ($breadcrumbs) {
        $breadcrumbs->parent('profile');
        $breadcrumbs->push('Change your password', route('change-password'));
    }
);


// add, edit, delete
// Transfer, Transaction, Account, beneficiary (fake), budget (fake),
//category (fake)

$objects = ['account', 'transaction', 'transfer', 'predictable'];
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
        'empty' . $object, function ($breadcrumbs, $date) use ($object) {
            $breadcrumbs->parent('home');
            $breadcrumbs->push(
                'Transactions without a ' . $object, route('empty' . $object)
            );
            if (!is_null($date)) {
                $breadcrumbs->push(
                    $date->format('F Y'), route(
                        'empty' . $object,
                        [$date->format('Y'), $date->format('m')]
                    )
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
                    $object . 'overview', $obj->id
                )
            );
            if ($date) {
                $breadcrumbs->push(
                    $date->format('F Y'), route(
                        $object . 'overview', $obj->id, $date->format('Y'),
                        $date->format('m')
                    )
                );
            }
        }
    );
}
