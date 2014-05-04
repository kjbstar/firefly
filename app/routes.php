<?php

// always authenticate home routes:
Route::when('/home*', 'auth');

// models:
Route::model('user', 'User');
Route::model('type', 'Type');
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

/*
 * ACCOUNT CONTROLLER
 *  */
Route::get('/home/account',['uses' => 'AccountController@index', 'as' => 'accounts']);
Route::get('/home/account/add',['uses' => 'AccountController@add', 'as' => 'addaccount']);
Route::get('/home/account/{account}/edit',['uses' => 'AccountController@edit', 'as' => 'editaccount']);
Route::get('/home/account/{account}/delete',['uses' => 'AccountController@delete', 'as' => 'deleteaccount']);

Route::get('/home/account/{account}/overview/chart/{year}/{month}',['uses' => 'AccountController@overviewChartByMonth']);

Route::get('/home/account/{account}/overview/{year}/{month}',['uses' => 'AccountController@overviewByMonth', 'as' => 'accountoverviewmonth']);
Route::get('/home/account/{account}/overview',['uses' => 'AccountController@overview', 'as' => 'accountoverview']);

Route::get('/home/account/{account}/overview/chart',['uses' => 'AccountController@overviewChart']);

Route::post('/home/account/add',['uses' => 'AccountController@postAdd', 'before' => 'csrf']);
Route::post('/home/account/{account}/edit',['uses' => 'AccountController@postEdit', 'before' => 'csrf']);
Route::post('/home/account/{account}/delete',['uses' => 'AccountController@postDelete', 'before' => 'csrf']);

/**
 * HOMECONTROLLER
 */
Route::get('/', ['uses' => 'HomeController@index', 'as' => 'index']);
Route::get('/home/{year?}/{month?}/{account?}',['uses' => 'HomeController@home', 'as' => 'home']);
Route::get('/home/predict/{year}/{month}/{day}',['uses' => 'HomeController@predict', 'as' => 'predictDay']);
Route::get('/home/recalc', ['uses' => 'PageController@recalculate', 'as' => 'recalc']);
Route::get('/home/flush', ['uses' => 'PageController@flush', 'as' => 'flush']);
Route::get('/home/decrypt', ['uses' => 'PageController@decrypt', 'as' => 'decrypt']);
Route::get('/home/search', ['uses' => 'SearchController@search', 'as' => 'search']);
Route::get('/home/moveComponents',['uses' => 'PageController@moveComponents','as' => 'moveComponents']);


/**
 * ALL META ROUTES:
 *  */
$objects = ['beneficiary', 'budget', 'category'];


/**
 * URL for components:
 */
Route::get('/home/type/{type}/typeahead',['uses' => 'ComponentController@typeahead']);
Route::get('/home/component/{type}/index',['uses' => 'ComponentController@index','as' => 'components']);
Route::get('/home/component/{type}/empty/{year?}/{month?}',['uses' => 'ComponentController@noComponent','as' => 'empty']);
Route::get('/home/component/{type}/add',['uses' => 'ComponentController@add','as' => 'addcomponent']);
Route::get('/home/component/{component}/overview',['uses' => 'ComponentController@overview','as' => 'componentoverview']);
Route::get('/home/component/{component}/overview/{year}/{month}',['uses' => 'ComponentController@overviewByMonth','as' => 'componentoverviewmonth']);
Route::get('/home/component/{component}/edit',['uses' => 'ComponentController@edit','as' => 'editcomponent']);
Route::get('/home/component/{component}/delete',['uses' => 'ComponentController@delete','as' => 'deletecomponent']);
Route::get('/home/component/icon/{component}',['uses' => 'ComponentController@renderIcon', 'as' => 'componenticon']);

Route::post('/home/component/{component}/delete',['uses' => 'ComponentController@postDelete','before' => 'csrf']);
Route::post('/home/component/{component}/edit',['uses' => 'ComponentController@postEdit','before' => 'csrf']);
Route::post('/home/component/{type}/add',['uses' => 'ComponentController@postAdd','before' => 'csrf']);

// URLS for LIMITS:
Route::get('/limit/add/{component}/{year}/{month}',['uses' => 'LimitController@add','as'   => 'addcomponentlimit']);
Route::get('/limit/edit/{limit}', ['uses' => 'LimitController@edit','as'   => 'editcomponentlimit']);
Route::get('/limit/delete/{limit}',['uses' => 'LimitController@delete','as'   => 'deletecomponentlimit']);

Route::post('/limit/add/{component}/{year}/{month}',['uses' => 'LimitController@postAdd','before'   => 'csrf']);
Route::post('/limit/edit/{limit}', ['uses' => 'LimitController@postEdit','before'   => 'csrf']);
Route::post('/limit/delete/{limit}',['uses' => 'LimitController@postDelete','before'   => 'csrf']);

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
Route::post('/home/piggy/drop',['uses' => 'PiggyController@dropPiggy']);
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

Route::get('/home/reports/period/{year}/{month}/chart',['uses' => 'ReportController@monthAccounts', 'as' => 'monthreportchart']);
Route::get('/home/reports/period/{year}/{month}/pie/{typename}',['uses' => 'ReportController@monthPieChart', 'as' => 'monthreportpie']);
Route::get('/home/reports/period/{year}/chart',['uses' => 'ReportController@yearAccounts', 'as' => 'yearreportchart']);

Route::get('/home/reports/compare/{year}/{otheryear}',['uses' => 'ReportController@compareYear', 'as' => 'compareyear']);

// compares:
#Route::get('/home/reports/compare/{year}/{otheryear}',['uses' => 'ReportController@yearCompare', 'as' => 'yearcompare']);
#Route::get('/home/reports/compare/{year}-{month}/{otheryear}-{othermonth}',['uses' => 'ReportController@monthCompare', 'as' => 'monthcompare']);

// charts:
#Route::get('/home/reports/year/{year}/ie',['uses' => 'ReportController@yearIeChart']);
#Route::get('/home/reports/year/{year}/components',['uses' => 'ReportController@yearComponentsChart']);
#Route::get('/home/reports/compare/{year}-{month}/{otheryear}-{othermonth}/account',['uses' => 'ReportController@monthCompareAccountChart']);




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
Route::get('/home/transaction',['uses' => 'TransactionController@index', 'as' => 'transactions']);
Route::get('/home/transaction/{transaction}/edit',['uses' => 'TransactionController@edit', 'as' => 'edittransaction']);
Route::get('/home/transaction/{transaction}/delete',['uses' => 'TransactionController@delete', 'as' => 'deletetransaction']);
Route::get('/home/transaction/add/{predictable?}',['uses' => 'TransactionController@add', 'as' => 'addtransaction']);

Route::post('/home/transaction/{transaction}/edit',['uses' => 'TransactionController@postEdit', 'before' => 'csrf']);
Route::post('/home/transaction/{transaction}/delete',['uses' => 'TransactionController@postDelete', 'before' => 'csrf']);
Route::post('/home/transaction/add/{predictable?}',['uses' => 'TransactionController@postAdd', 'before' => 'csrf']);

/**
 * TRANSFER CONTROLLER
 */
Route::get('/home/transfer',['uses' => 'TransferController@index', 'as' => 'transfers']);
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
Route::get('/logout', ['uses' => 'UserController@logout','as' => 'logout']);
Route::get('/reset', ['uses' => 'UserController@reset', 'as' => 'reset']);
Route::get('/register', ['uses' => 'UserController@register', 'as' => 'register']);
Route::get('/activate/{code}', ['uses' => 'UserController@activate','as' => 'activate']);
Route::get('/resetme/{code}', 'UserController@resetme');
Route::post('/reset', ['uses' => 'UserController@postReset', 'before' => 'csrf']);
Route::post('/login', ['uses' => 'UserController@postLogin', 'before' => 'csrf']);
Route::post('/register', ['uses' => 'UserController@postRegister', 'before' => 'csrf']);

/**
 * Profile controller:
 */
Route::get('/home/profile', ['uses' => 'ProfileController@index','as' => 'profile']);
Route::get('/home/profile/password', ['uses' => 'ProfileController@changePassword','as' => 'change-password']);
Route::get('/home/profile/username', ['uses' => 'ProfileController@changeUsername','as' => 'change-username']);

Route::post('/home/profile/password', ['uses' => 'ProfileController@postChangePassword','before' => 'csrf']);
Route::post('/home/profile/username', ['uses' => 'ProfileController@postChangeUsername','before' => 'csrf']);