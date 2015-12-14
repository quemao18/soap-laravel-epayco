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
	$app->post('/login','App\Http\Controllers\AuthController@login');
	$app->post('/register','App\Http\Controllers\AuthController@register');
	$app->get('/domain-search', 'App\Http\Controllers\DomainController@search');
	$app->get('/postcode-search', 'App\Http\Controllers\PostcodeController@search');
	$app->get('/image-search', 'App\Http\Controllers\ImageController@search');
	$app->get('/fonts', 'App\Http\Controllers\FontController@listFonts');

});

$app->group(['middleware' => ['apikey', 'auth']], function($app) {
	$app->get('/account', 'App\Http\Controllers\AuthController@getAccount');
	$app->post('/account', 'App\Http\Controllers\AuthController@updateAccount');

	$app->get('/sites', 'App\Http\Controllers\SiteController@getSites');
	$app->post('/site/cancel', 'App\Http\Controllers\SiteController@cancelSite');

	$app->get('/documents', 'App\Http\Controllers\DocumentController@getDocuments');
	$app->get('/document/{id}', 'App\Http\Controllers\DocumentController@getDocument');
	$app->post('/document', 'App\Http\Controllers\DocumentController@createDocument');
	$app->post('/document/{id}', 'App\Http\Controllers\DocumentController@updateDocument');
});

$app->group(['middleware' => ['apikey', 'site']], function($app) {
	$app->get('/site', 'App\Http\Controllers\SiteController@getSite');
	$app->post('/site', 'App\Http\Controllers\SiteController@updateSite');

	$app->get('/pages', 'App\Http\Controllers\PageController@getPages');
	$app->get('/page/{page}', 'App\Http\Controllers\PageController@getPage');
	$app->post('/pages/update', 'App\Http\Controllers\PageController@updatePages');

	$app->get('/services', 'App\Http\Controllers\ServiceController@getServices');
	$app->get('/service/{url}', 'App\Http\Controllers\ServiceController@getService');
	$app->post('/services/update', 'App\Http\Controllers\ServiceController@updateServices');

	$app->get('/packages', 'App\Http\Controllers\PackageController@getPackages');
	$app->get('/package/{url}', 'App\Http\Controllers\PackageController@getPackage');
	$app->post('/packages/update', 'App\Http\Controllers\PackageController@updatePackages');

	// Quotes Routes
	$app->get('/quotes', 'App\Http\Controllers\QuoteController@getQuotes');
	$app->get('/quote/{id}', 'App\Http\Controllers\QuoteController@getQuote');
	$app->post('/quote/create', 'App\Http\Controllers\QuoteController@createQuote');
	$app->post('/quote/update/{id}', 'App\Http\Controllers\QuoteController@updateQuote');
	$app->post('/quote/delete/{id}', 'App\Http\Controllers\QuoteController@deleteQuote');

	// Enquiries Routes
	$app->get('/enquiries', 'App\Http\Controllers\EnquiryController@getEnquiries');
	$app->get('/enquiry/{id}', 'App\Http\Controllers\EnquiryController@getEnquiry');
	$app->post('/enquiry/create', 'App\Http\Controllers\EnquiryController@createEnquiry');
	$app->post('/enquiry/update/{id}', 'App\Http\Controllers\EnquiryController@updateEnquiry');
	$app->post('/enquiry/delete/{id}', 'App\Http\Controllers\EnquiryController@deleteEnquiry');
});