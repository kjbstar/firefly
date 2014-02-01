<?php

// always authenticate home routes:
Route::when('/home*', 'auth');

// models:
Route::model('user', 'User');

Route::bind('account', function ($value, $route) {return Auth::user()->accounts()->find($value);});
Route::bind('component', function ($value, $route) {return Auth::user()->components()->find($value);});
Route::bind('piggybank', function ($value, $route) {return Auth::user()->piggybanks()->find($value);});
Route::bind('setting', function ($value, $route) {return Auth::user()->settings()->find($value);});
Route::bind('transaction', function ($value, $route) {return Auth::user()->transactions()->find($value);});
Route::bind('transfer', function ($value, $route) {return Auth::user()->transfers()->find($value);});


Route::bind('limit', function ($value, $route) {
        $limit = Limit::find($value);
        $budget = $limit->component()->first();
        if ($budget->user_id == Auth::user()->id) {
            return $limit;
        }
        return false;
    }
);


// some common patterns:
Route::pattern('id', '[0-9]+');
Route::pattern('month', '[0-9]+');
Route::pattern('year', '20[0-9]{2}');
Route::pattern('otheryear', '20[0-9]{2}');

/*
 * Misc new pages. TODO move.
 * */
Route::get('/home/slow', ['uses' => 'SlowController@index', 'as' => 'slow']);
Route::get('/home/reports/compared/{year}/{otheryear}',['as' => 'year_compare', 'uses' => 'ReportController@compareYears']);
Route::get('/home/reports/compared/chart/{component}/{year}/{otheryear}',['as' => 'year_compare_chart','uses' => 'ReportController@compareComponentChart']);

/**
 * HOMECONTROLLER
 */
Route::get('/', ['uses' => 'HomeController@showIndex', 'as' => 'index']);
Route::get('/home/{year?}/{month?}',['uses' => 'HomeController@showHome', 'as' => 'home']);
Route::get('/home/charts/{chart}/{year?}/{month?}',['uses' => 'HomeController@showChart', 'as' => 'homechart']);
Route::get('/home/recalc', ['uses' => 'PageController@recalculate', 'as' => 'recalc']);


/**
 * LIST CONTROLLER ROUTES
 */
Route::get('/home/list/{id}/{year}/{month}/{type}',['uses' => 'ListController@showList']);

/**
 * ALL META ROUTES:
 *  */
$objects = ['beneficiary', 'budget', 'category'];


foreach ($objects as $o) {

    /**
     * ALL POST META ROUTES:
     */
    Route::group(
        ['before' => 'meta|csrf', 'prefix' => 'home/' . $o], function () {
            Route::post('/{component}/edit', ['uses' => 'MetaController@postEdit']);
            Route::post('/{component}/delete', ['uses' => 'MetaController@postDelete']);
            Route::post('/add', ['uses' => 'MetaController@postAdd']);
            Route::post('/limit/add/{component}/{year}/{month}',['uses' => 'LimitController@postAddLimit']);
            Route::post('/limit/edit/{limit}',['uses' => 'LimitController@postEditLimit']);
            Route::post('/limit/delete/{limit}',['uses' => 'LimitController@postDeleteLimit']);
        }
    );
    /**
     * ALL GET META ROUTES
     */
    Route::group(
        ['before' => 'meta', 'prefix' => 'home/' . $o], function () use ($o) {
            Route::get('',['uses' => 'MetaController@showIndex', 'as' => Str::plural($o)]);
            Route::get('/add', ['uses' => 'MetaController@add', 'as' => 'add' . $o]);
            Route::get('/empty/{year?}/{month?}',['uses' => 'MetaController@showEmpty', 'as' => 'empty' . $o]);
            Route::get('/index/average', ['uses' => 'MetaController@showAverageChart']);
            Route::get('/typeahead', ['uses' => 'MetaController@typeahead']);
            Route::get('/{component}/edit',['uses' => 'MetaController@edit', 'as' => 'edit' . $o]);
            Route::get('/{component}/delete',['uses' => 'MetaController@delete', 'as' => 'delete' . $o]);
            Route::get('/{component}/overview/chart/{year?}/{month?}',['uses' => 'MetaController@showOverviewChart','as'   => $o . 'overviewchart']);
            Route::get('/{component}/overview/{year?}/{month?}',['uses' => 'MetaController@showOverview','as'   => $o . 'overview']);
            Route::get('/limit/add/{component}/{year}/{month}',['uses' => 'LimitController@addLimit','as'   => 'add' . $o . 'limit']);
            Route::get('/limit/edit/{limit}', ['uses' => 'LimitController@editLimit','as'   => 'edit' . $o . 'limit']);
            Route::get('/limit/delete/{limit}',['uses' => 'LimitController@deleteLimit','as'   => 'delete' . $o . 'limit']);
        }
    );
}

/**
 * METACONTROLLER ROUTES (extra)
 */
Route::get('/home/meta/piechart', ['uses' => 'MetaController@showPieChart']);

/**
 * PIGGY BANK CONTROLLER
 */
Route::get('/home/piggy',['uses' => 'PiggyController@index','as' => 'piggy']);
Route::get('/home/piggy/add',['uses' => 'PiggyController@add','as' => 'addpiggybank']);
Route::get('/home/piggy/select',['uses' => 'PiggyController@selectAccount','as' => 'piggyselect']);
Route::get('/home/piggy/edit/{piggybank}',['uses' => 'PiggyController@edit','as' => 'editpiggy']);
Route::get('/home/piggy/delete/{piggybank}',['uses' => 'PiggyController@delete','as' => 'deletepiggy']);
Route::get('/home/piggy/amount/{piggybank}',['uses' => 'PiggyController@updateAmount','as' => 'piggyamount']);
Route::post('/home/piggy/add',['uses' => 'PiggyController@postAdd','before' => 'csrf']);
Route::post('/home/piggy/select',['uses' => 'PiggyController@postSelectAccount','before' => 'csrf']);
Route::post('/home/piggy/edit/{piggybank}',['uses' => 'PiggyController@postEdit','before' => 'csrf']);
Route::post('/home/piggy/delete/{piggybank}',['uses' => 'PiggyController@postDelete','before' => 'csrf']);
Route::post('/home/piggy/amount/{piggybank}',['uses' => 'PiggyController@postUpdateAmount','before' => 'csrf']);



/**
 * REPORTCONTROLLER
 */
Route::get('/home/reports', ['uses' => 'ReportController@showIndex', 'as' => 'reports']);
Route::get('/home/report/{year}',['uses' => 'ReportController@showYearlyReport', 'as' => 'yearreport']);
Route::get('/home/report/{year}/networth', ['uses' => 'ReportController@netWorthChart']);
Route::get('/home/report/{year}/chart/overview/{component}',['uses' => 'ReportController@objectOverviewChart']);
Route::get('/home/report/{year}/chart/{type}/{sort}',['uses' => 'ReportController@objectChart']);


/**
 * SETTINGSCONTROLLER (and allowances)
 */
Route::get('/home/allowances',['uses' => 'SettingsController@allowances', 'as' => 'allowances']);
Route::get('/home/allowance/{setting}/edit',['uses' => 'SettingsController@editAllowance', 'as' => 'editallowance']);
Route::get('/home/allowance/{setting}/delete',['uses' => 'SettingsController@deleteAllowance', 'as' => 'deleteallowance']);
Route::get('/home/allowances/add',['uses' => 'SettingsController@addAllowance', 'as' => 'addallowance']);
Route::get('/home/settings', ['uses' => 'SettingsController@index', 'as' => 'settings']);
Route::post('/home/allowances', ['uses' => 'SettingsController@postAllowances']);
Route::post('/home/allowance/{setting}/edit',['uses' => 'SettingsController@postEditAllowance']);
Route::post('/home/allowance/{setting}/delete',['uses' => 'SettingsController@postDeleteAllowance']);
Route::post('/home/allowances/add', ['uses' => 'SettingsController@postAddAllowance']);
Route::post('/home/settings', ['uses' => 'SettingsController@postIndex']);




/**
 * TRANSACTION CONTROLLER
 */
Route::get('/home/transaction',['uses' => 'TransactionController@showIndex', 'as' => 'transactions']);
Route::get('/home/transaction/{transaction}/edit',['uses' => 'TransactionController@edit', 'as' => 'edittransaction']);
Route::get('/home/transaction/{transaction}/delete',['uses' => 'TransactionController@delete', 'as' => 'deletetransaction']);
Route::get('/home/transaction/add/{account?}',['uses' => 'TransactionController@add', 'as' => 'addtransaction']);
Route::post('/home/transaction/{transaction}/edit',['uses' => 'TransactionController@postEdit', 'before' => 'csrf']);
Route::post('/home/transaction/{transaction}/delete',['uses' => 'TransactionController@postDelete', 'before' => 'csrf']);
Route::post('/home/transaction/add/{account?}',['uses' => 'TransactionController@postAdd', 'before' => 'csrf']);

/**
 * TRANSFER CONTROLLER
 */
Route::get('/home/transfer',['uses' => 'TransferController@showIndex', 'as' => 'transfers']);
Route::get('/home/transfer/{transfer}/edit',['uses' => 'TransferController@edit', 'as' => 'edittransfer']);
Route::get('/home/transfer/{transfer}/delete',['uses' => 'TransferController@delete', 'as' => 'deletetransfer']);
Route::get('/home/transfer/add/{account?}',['uses' => 'TransferController@add', 'as' => 'addtransfer']);
Route::post('/home/transfer/{transfer}/edit',['uses' => 'TransferController@postEdit', 'before' => 'csrf']);
Route::post('/home/transfer/{transfer}/delete',['uses' => 'TransferController@postDelete', 'before' => 'csrf']);
Route::post('/home/transfer/add/{account?}',['uses' => 'TransferController@postAdd', 'before' => 'csrf']);

/**
 * USER CONTROLLER:
 */
Route::get('/login', ['uses' => 'UserController@login', 'as' => 'login']);
Route::get('/logout', 'UserController@logout');
Route::get('/reset', ['uses' => 'UserController@reset', 'as' => 'reset']);
Route::get('/register', ['uses' => 'UserController@register', 'as' => 'register']);
Route::get('/activate/{code}', 'UserController@activate');
Route::get('/resetme/{code}', 'UserController@resetme');
Route::post('/reset', ['uses' => 'UserController@doReset', 'before' => 'csrf']);
Route::post('/login', ['uses' => 'UserController@doLogin', 'before' => 'csrf']);
Route::post('/register', ['uses' => 'UserController@doRegister', 'before' => 'csrf']);


/*
 * ACCOUNT CONTROLLER
 *  */
Route::get('/home/account',['uses' => 'AccountController@showIndex', 'as' => 'accounts']);
Route::get('/home/account/add',['uses' => 'AccountController@add', 'as' => 'addaccount']);
Route::get('/home/account/{account}/edit',['uses' => 'AccountController@edit', 'as' => 'editaccount']);
Route::get('/home/account/{account}/delete',['uses' => 'AccountController@delete', 'as' => 'deleteaccount']);
Route::get('/home/account/{account}/overview/{year?}/{month?}',['uses' => 'AccountController@showOverview', 'as' => 'accountoverview']);
Route::get('/home/account/{account}/overview/chart/{year?}/{month?}',['uses' => 'AccountController@showChartOverview','as'   => 'accountoverviewchart']);
Route::post('/home/account/add',['uses' => 'AccountController@postAdd', 'before' => 'csrf']);
Route::post('/home/account/{account}/edit',['uses' => 'AccountController@postEdit', 'before' => 'csrf']);
Route::post('/home/account/{account}/delete',['uses' => 'AccountController@postDelete', 'before' => 'csrf']);