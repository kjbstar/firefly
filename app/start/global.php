<?php

/*
|--------------------------------------------------------------------------
| Register The Laravel Class Loader
|--------------------------------------------------------------------------
|
| In addition to using Composer, you may use the Laravel class loader to
| load your controllers and models. This is useful for keeping all of
| your classes in the "global" namespace without Composer updating.
|
*/

ClassLoader::addDirectories(
    array(

    app_path() . '/commands', app_path() . '/controllers',
    app_path() . '/models', app_path() . '/database/seeds',


    )
);

/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
| Here we will configure the error logger setup for the application which
| is built on top of the wonderful Monolog library. By default we will
| build a rotating log file setup which creates a new file each day.
|
*/

$logFile = 'log-' . php_sapi_name() . '.txt';

Log::useDailyFiles(storage_path() . '/logs/' . $logFile);

/*
|--------------------------------------------------------------------------
| Application Error Handler
|--------------------------------------------------------------------------
|
| Here you may handle any errors that occur in your application, including
| logging them or displaying custom views for specific errors. You may
| even register several error handlers to handle different types of
| exceptions. If nothing is returned, the default error view is
| shown, which includes a detailed stack trace during debug.
|
*/

App::error(
    function (Exception $exception, $code) {
        Log::error($exception);
    }
);

/*
|--------------------------------------------------------------------------
| Maintenance Mode Handler
|--------------------------------------------------------------------------
|
| The "down" Artisan command gives you the ability to put an application
| into maintenance mode. Here, you will define what is displayed back
| to the user if maintenace mode is in effect for this application.
|
*/

App::down(
    function () {
        return Response::make("Be right back!", 503);
    }
);

/*
|--------------------------------------------------------------------------
| Require The Filters File
|--------------------------------------------------------------------------
|
| Next we will load the filters file for the application. This gives us
| a nice separate location to store our route and application filter
| definitions instead of putting them all in the main routes file.
|
*/
function mf($n, $coloured = false, $list = true)
{
    $n = floatval($n);
    $string = number_format($n, 2, ',', '.');
    if ($list === true) {
        $string = str_pad($string, 10, '_', STR_PAD_LEFT);
        $string = str_replace('_', '&nbsp;', $string);
    }

    if ($coloured === true && $n === 0.0) {
        return '<span style="color:#999" class="money">&#8364;' . $string
        . '</span>';
    }
    if ($coloured === true && $n > 0) {
        return '<span class="text-success money">&#8364;' . $string . '</span>';
    }
    if ($coloured === true && $n < 0) {
        return '<span class="text-danger money">&#8364;' . $string . '</span>';
    }

    return '<span class="money">&#8364; ' . $string . '</span>';
}

require app_path() . '/filters.php';
require app_path() . '/events/TransactionTrigger.php';
require app_path() . '/events/TransferTrigger.php';
require app_path() . '/events/AccountTrigger.php';