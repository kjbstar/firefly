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
Route::bind('predictable', function ($value, $route) {return Auth::user()->predictables()->find($value);});


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
Route::pattern('day', '[0-9]+');
Route::pattern('year', '20[0-9]{2}');
Route::pattern('otheryear', '20[0-9]{2}');
Route::pattern('othermonth', '[0-9]+');

/**
 * HOMECONTROLLER
 */
Route::get('/', ['uses' => 'HomeController@showIndex', 'as' => 'index']);
Route::get('/home/{year?}/{month?}',['uses' => 'HomeController@showHome', 'as' => 'home']);
Route::get('/home/predict/{year}/{month}/{day}',['uses' => 'HomeController@predict', 'as' => 'predictDay']);
Route::get('/home/recalc', ['uses' => 'PageController@recalculate', 'as' => 'recalc']);



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
            Route::post('/{component}/edit', ['uses' => 'ComponentController@postEdit']);
            Route::post('/{component}/delete', ['uses' => 'ComponentController@postDelete']);
            Route::post('/add', ['uses' => 'ComponentController@postAdd']);
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
            Route::get('',['uses' => 'ComponentController@showIndex', 'as' => Str::plural($o)]);
            Route::get('/add', ['uses' => 'ComponentController@add', 'as' => 'add' . $o]);
            Route::get('/empty/{year?}/{month?}',['uses' => 'ComponentController@showEmpty', 'as' => 'empty' . $o]);
            Route::get('/index/average', ['uses' => 'ComponentController@showAverageChart']);
            Route::get('/typeahead', ['uses' => 'ComponentController@typeahead']);
            Route::get('/{component}/edit',['uses' => 'ComponentController@edit', 'as' => 'edit' . $o]);
            Route::get('/{component}/delete',['uses' => 'ComponentController@delete', 'as' => 'delete' . $o]);
            Route::get('/{component}/overview/chart/{year?}/{month?}',['uses' => 'ComponentController@showOverviewChart','as'   => $o . 'overviewchart']);
            Route::get('/{component}/overview/{year?}/{month?}',['uses' => 'ComponentController@showOverview','as'   => $o . 'overview']);
            Route::get('/limit/add/{component}/{year}/{month}',['uses' => 'LimitController@addLimit','as'   => 'add' . $o . 'limit']);
            Route::get('/limit/edit/{limit}', ['uses' => 'LimitController@editLimit','as'   => 'edit' . $o . 'limit']);
            Route::get('/limit/delete/{limit}',['uses' => 'LimitController@deleteLimit','as'   => 'delete' . $o . 'limit']);
        }
    );
}

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
 * PREDICTABLE CONTROLLER
 */
Route::get('/home/predictable',['uses' => 'PredictableController@index', 'as' => 'predictables']);
Route::get('/home/predictable/{predictable}/edit',['uses' => 'PredictableController@edit', 'as' => 'editpredictable']);
Route::get('/home/predictable/{predictable}/delete',['uses' => 'PredictableController@delete', 'as' => 'deletepredictable']);
Route::get('/home/predictable/add/{transaction?}',['uses' => 'PredictableController@add', 'as' => 'addpredictable']);
Route::get('/home/predictable/{predictable}/overview',['uses' => 'PredictableController@overview','as'   =>'predictableoverview']);
Route::get('/home/predictable/{predictable}/rescan',['uses' => 'PredictableController@rescan', 'as' => 'rescanpredictable']);
Route::get('/home/predictable/{predictable}/rescan-all',['uses' => 'PredictableController@rescanAll', 'as' => 'rescanallpredictable']);


Route::post('/home/predictable/{predictable}/edit',['uses' => 'PredictableController@postEdit', 'before' => 'csrf']);
Route::post('/home/predictable/{predictable}/delete',['uses' => 'PredictableController@postDelete', 'before' => 'csrf']);
Route::post('/home/predictable/add/{transaction?}',['uses' => 'PredictableController@postAdd', 'before' => 'csrf']);


/**
 * REPORTSCONTROLLER
 */
Route::get('/home/reports',['uses' => 'ReportController@index', 'as' => 'reports']);
Route::get('/home/reports/period/{year}',['uses' => 'ReportController@year', 'as' => 'yearreport']);
Route::get('/home/reports/period/{year}/{month}',['uses' => 'ReportController@month', 'as' => 'monthreport']);

// compares:
Route::get('/home/reports/compare/{year}/{otheryear}',['uses' => 'ReportController@yearCompare', 'as' => 'yearcompare']);
Route::get('/home/reports/compare/{year}-{month}/{otheryear}-{othermonth}',['uses' => 'ReportController@monthCompare', 'as' => 'monthcompare']);

// charts:
Route::get('/home/reports/year/{year}/ie',['uses' => 'ReportController@yearIeChart']);
Route::get('/home/reports/year/{year}/components',['uses' => 'ReportController@yearComponentsChart']);
Route::get('/home/reports/compare/{year}-{month}/{otheryear}-{othermonth}/account',['uses' => 'ReportController@monthCompareAccountChart']);




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
Route::get('/home/transaction/add/{predictable?}',['uses' => 'TransactionController@add', 'as' => 'addtransaction']);

Route::post('/home/transaction/{transaction}/edit',['uses' => 'TransactionController@postEdit', 'before' => 'csrf']);
Route::post('/home/transaction/{transaction}/delete',['uses' => 'TransactionController@postDelete', 'before' => 'csrf']);
Route::post('/home/transaction/add/{predictable?}',['uses' => 'TransactionController@postAdd', 'before' => 'csrf']);

/**
 * TRANSFER CONTROLLER
 */
Route::get('/home/transfer',['uses' => 'TransferController@showIndex', 'as' => 'transfers']);
Route::get('/home/transfer/{transfer}/edit',['uses' => 'TransferController@edit', 'as' => 'edittransfer']);
Route::get('/home/transfer/{transfer}/delete',['uses' => 'TransferController@delete', 'as' => 'deletetransfer']);
Route::get('/home/transfer/add',['uses' => 'TransferController@add', 'as' => 'addtransfer']);
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
Route::get('/activate/{code}', ['uses' => 'UserController@activate','as' => 'activate']);
Route::get('/resetme/{code}', 'UserController@resetme');
Route::post('/reset', ['uses' => 'UserController@postReset', 'before' => 'csrf']);
Route::post('/login', ['uses' => 'UserController@postLogin', 'before' => 'csrf']);
Route::post('/register', ['uses' => 'UserController@postRegister', 'before' => 'csrf']);


/*
 * ACCOUNT CONTROLLER
 *  */
Route::get('/home/account',['uses' => 'AccountController@showIndex', 'as' => 'accounts']);
Route::get('/home/account/add',['uses' => 'AccountController@add', 'as' => 'addaccount']);
Route::get('/home/account/{account}/edit',['uses' => 'AccountController@edit', 'as' => 'editaccount']);
Route::get('/home/account/{account}/delete',['uses' => 'AccountController@delete', 'as' => 'deleteaccount']);
Route::get('/home/account/{account}/overview/{year?}/{month?}',['uses' => 'AccountController@showOverview', 'as' => 'accountoverview']);
Route::get('/home/account/{account}/overview/chart/{year?}/{month?}',['uses' => 'AccountController@showChartOverview','as'   => 'accountoverviewchart']);
Route::get('/home/account/overview/chart/{year?}/{month?}',['uses' => 'AccountController@showChartAllOverview','as'   => 'allaccountoverviewchart']);
Route::post('/home/account/add',['uses' => 'AccountController@postAdd', 'before' => 'csrf']);
Route::post('/home/account/{account}/edit',['uses' => 'AccountController@postEdit', 'before' => 'csrf']);
Route::post('/home/account/{account}/delete',['uses' => 'AccountController@postDelete', 'before' => 'csrf']);