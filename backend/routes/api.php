<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::group(['middleware' => 'api'], function() {
    Route::get('organisation/private', 'Api\OrganisationController@index');
    Route::get('clients', 'Api\ClientsController@index');
    Route::get('suppliers', 'Api\SuppliersController@index');
    Route::resource('contacts', 'Api\ContactController', ['only' => ['index', 'show', 'store', 'update']]);
    Route::get('invoices', 'Api\InvoiceController@index');
    Route::post('invoice/{id}', 'Api\InvoiceController@create_invoice');
    Route::resource('accounts', 'Api\AccountController', ['only' => ['index', 'show', 'store', 'update']]);
    Route::resource('bank-transfers', 'Api\BankTransferController', ['only' => ['index', 'show', 'store']]);
    Route::resource('bank-transactions', 'Api\BankTransactionsController', ['only' => ['index', 'show', 'store', 'update']]);
});
