<?php
use Carbon\Carbon as Carbon;
Breadcrumbs::register(
    'home', function ($breadcrumbs) {
        $breadcrumbs->push('Home', route('home'));
    }
);

Breadcrumbs::register(
    'accounts', function ($breadcrumbs) {
        $breadcrumbs->parent('home');
        $breadcrumbs->push('All accounts', route('accounts'));
    }
);

Breadcrumbs::register(
    'account', function ($breadcrumbs,Account $account,Carbon $date = null) {
        $breadcrumbs->parent('accounts');

        $breadcrumbs->push($account->name, route('accountoverview',
                $account->id));
        if($date) {
            $breadcrumbs->push($date->format('F Y'), route('accountoverview',
                    $account->id,$date->format('Y'),$date->format('m')));
        }
    }
);