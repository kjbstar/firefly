<?php

// always authenticate home routes:
Route::when('/home*', 'auth');

// models:
Route::model('user', 'User');

// better model filters (to be defined)
Route::bind('component', function($value, $route) {
        return Auth::user()->components()->find($value);
    });

Route::bind('account', function($value, $route) {
        return Auth::user()->accounts()->find($value);
    });

Route::bind('limit', function($value, $route) {
//        return Auth::user()->limits()->find($value);
        $limit = Limit::find($value);
        $budget = $limit->component()->first();
        if($budget->user_id == Auth::user()->id) {
            return $limit;
        }
        return false;
    });
Route::bind('transaction', function($value, $route) {
        return Auth::user()->transactions()->find($value);
    });

Route::bind('transfer', function($value, $route) {
        return Auth::user()->transfers()->find($value);
    });



// some common patterns:
Route::pattern('id', '[0-9]+');
Route::pattern('month', '[0-9]+');
Route::pattern('year', '20[0-9]{2}');

// chart route (not sure why on top)
Route::get('/home/charts/{chart}/{year?}/{month?}',['uses' => 'HomeController@showChart', 'as' => 'homechart']);

// not logged in (GET routes):
Route::get('/', ['uses' => 'HomeController@showIndex', 'as' => 'index']);

// main home page and the month switch.
Route::get('/home/{year?}/{month?}',['uses' => 'HomeController@showHome','as' => 'home']);

Route::get('/login', ['uses' => 'UserController@login', 'as' => 'login']);
Route::get('/logout', 'UserController@logout');
Route::get('/reset', ['uses' => 'UserController@reset', 'as' => 'reset']);
Route::get('/register', ['uses' => 'UserController@register', 'as' => 'register']);
Route::get('/activate/{code}', 'UserController@activate');
Route::get('/resetme/{code}', 'UserController@resetme');

// not logged in (POST routes)
Route::post('/reset', ['uses' => 'UserController@doReset', 'before' => 'csrf']);
Route::post('/login', ['uses' => 'UserController@doLogin', 'before' => 'csrf']);
Route::post('/register', ['uses' => 'UserController@doRegister', 'before' => 'csrf']);

/*
 * Routes for user settings:
 */
Route::get('/home/settings', ['uses' => 'SettingsController@index', 'as' => 'settings']);


/**
 * NEW META ROUTES:
 *  */
$objects = ['beneficiary', 'budget', 'category'];


foreach ($objects as $o) {
    // all POST routes
    Route::group(['before' => 'meta|csrf','prefix' => 'home/' . $o], function()
        {
            Route::post('/add',['uses' => 'MetaController@postAdd']);
            Route::post('/{component}/edit',['uses' => 'MetaController@postEdit']);
            Route::post('/{component}/delete',['uses' => 'MetaController@postDelete']);
            Route::post('/limit/add/{component}/{year}/{month}',['uses' => 'LimitController@postAddLimit']);
            Route::post('/limit/edit/{limit}',['uses' => 'LimitController@postEditLimit']);
            Route::post('/limit/delete/{limit}',['uses' => 'LimitController@postDeleteLimit']);


        });
    // all GET routes.
    Route::group(['before' => 'meta','prefix' => 'home/' . $o], function() use ($o)
        {
            Route::get('',['uses'  => 'MetaController@showIndex','as' => Str::plural($o)]);
            Route::get('/empty',['uses'  => 'MetaController@showEmpty', 'as' => 'empty' . $o]);
            Route::get('/typeahead',['uses' => 'MetaController@typeahead', 'before' => 'meta']);
            Route::get('/{component}/overview/{year?}/{month?}',['uses'  => 'MetaController@showOverview', 'as' => $o . 'overview']);
            Route::get('/{component}/overview/chart/{year?}/{month?}',['uses' => 'MetaController@showOverviewChart','as'    => $o . 'overviewchart']);
            Route::get('/add',['uses' => 'MetaController@add','as' => 'add' . $o]);
            Route::get('/{component}/edit',['uses'  => 'MetaController@edit', 'as' => 'edit' . $o]);
            Route::get('/{component}/delete',['uses'  => 'MetaController@delete', 'as' => 'delete' . $o]);
            Route::get('/limit/add/{component}/{year}/{month}',['uses'  => 'LimitController@addLimit', 'as' => 'add' . $o . 'limit']);
            Route::get('/limit/edit/{limit}',['uses'  => 'LimitController@editLimit', 'as' => 'edit' . $o . 'limit']);
            Route::get('/limit/delete/{limit}',['uses' => 'LimitController@deleteLimit','as'    => 'delete' . $o . 'limit']);
        });






}


Route::get('/home/meta/piechart', ['uses' => 'MetaController@showPieChart']);
Route::get('/home/list/{id}/{year}/{month}/{type}',['uses' => 'ListController@showList']);


/**
 * All (new) transaction routes:
 */
Route::get('/home/transaction',['uses' => 'TransactionController@showIndex', 'as' => 'transactions']);
Route::get('/home/transaction/{transaction}/edit',['uses' => 'TransactionController@edit', 'as' => 'edittransaction']);
Route::post('/home/transaction/{transaction}/edit', ['uses' => 'TransactionController@postEdit','before' => 'csrf']);
Route::get('/home/transaction/{transaction}/delete',['uses' => 'TransactionController@delete', 'as' => 'deletetransaction']);
Route::post('/home/transaction/{transaction}/delete',['uses' => 'TransactionController@postDelete','before' => 'csrf']);
Route::get('/home/transaction/add/{account?}',['uses' => 'TransactionController@add', 'as' => 'addtransaction']);
Route::post('/home/transaction/add/{account?}',['uses' => 'TransactionController@postAdd','before' => 'csrf']);

/*
 * All (new) transfer routes
 */
Route::get('/home/transfer',['uses' => 'TransferController@showIndex', 'as' => 'transfers']);
Route::get('/home/transfer/{transfer}/edit',['uses' => 'TransferController@edit', 'as' => 'edittransfer']);
Route::post('/home/transfer/{transfer}/edit', ['uses' => 'TransferController@postEdit','before' => 'csrf']);
Route::get('/home/transfer/{transfer}/delete',['uses' => 'TransferController@delete', 'as' => 'deletetransfer']);
Route::post('/home/transfer/{transfer}/delete', ['uses' => 'TransferController@postDelete','before' => 'csrf']);
Route::get('/home/transfer/add/{account?}',['uses' => 'TransferController@add', 'as' => 'addtransfer']);
Route::post('/home/transfer/add/{account?}',['uses' => 'TransferController@postAdd','before' => 'csrf']);

/*
 * All (new) account routes.
 *  */
Route::get('/home/account',['uses' => 'AccountController@showIndex', 'as' => 'accounts']);
Route::get('/home/account/add',['uses' => 'AccountController@add', 'as' => 'addaccount']);
Route::post('/home/account/add',['uses' => 'AccountController@postAdd', 'before' => 'csrf']);
Route::get('/home/account/{account}/edit',['uses' => 'AccountController@edit', 'as' => 'editaccount']);
Route::post('/home/account/{account}/edit',['uses' => 'AccountController@postEdit', 'before' => 'csrf']);
Route::get('/home/account/{account}/delete',['uses' => 'AccountController@delete', 'as' => 'deleteaccount']);
Route::post('/home/account/{account}/delete',['uses' => 'AccountController@postDelete', 'before' => 'csrf']);

Route::get('/home/account/{account}/overview/{year?}/{month?}',['uses' => 'AccountController@showOverview', 'as' => 'accountoverview']);
Route::get('/home/account/{account}/overview/chart/{year?}/{month?}',['uses' => 'AccountController@showChartOverview','as'    => 'accountoverviewchart']);


Route::get('/home/refine', 'PageController@refineTransactions');
Route::get('/home/recalc', ['uses' => 'PageController@recalculate','as' => 'recalc']);