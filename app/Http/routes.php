<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
	return $app->welcome();
});

$app->routeMiddleware([
	'apikey' => 'App\Http\Middleware\APIKeyMiddleware',
	'site' => 'App\Http\Middleware\SiteMiddleware',
	'auth' => 'App\Http\Middleware\AuthMiddleware'
]);

$app->group(['middleware' => 'apikey'], function($app) {
	$app->post('/balance','App\Http\Controllers\AuthController@balance');
	$app->post('/register','App\Http\Controllers\AuthController@register');
	$app->post('/wallet/add', 'App\Http\Controllers\WalletController@addMoney');
	$app->post('/wallet/update', 'App\Http\Controllers\WalletController@updateTransaction');
});

$app->group(['middleware' => ['apikey', 'auth']], function($app) {
	$app->get('/account', 'App\Http\Controllers\AuthController@getAccount');
	$app->post('/account', 'App\Http\Controllers\AuthController@updateAccount');
	$app->post('/wallet/send', 'App\Http\Controllers\WalletController@sendMoney');
});

$app->group(['middleware' => ['apikey', 'site']], function($app) {
	
});
