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
    Route::get('storage/invoice', 'Api\StorageController@invoice_template_path');
    Route::get('clients', 'Api\ClientsController@index');
    Route::get('suppliers', 'Api\SuppliersController@index');
    Route::resource('contacts', 'Api\ContactController', ['only' => ['index', 'show', 'store', 'update']]);
    Route::get('contact/infos', 'Api\ContactController@contact_infos');
    Route::get('invoices', 'Api\InvoiceController@index');
    Route::get('invoices/{id}', 'Api\InvoiceController@show');
    Route::put('invoices/{id}', 'Api\InvoiceController@update_invoice');
    Route::post('invoice/{id}', 'Api\InvoiceController@create_invoice');
    Route::get('invoice/{id}', 'Api\InvoiceController@contact_invoices');
    Route::post('upload_invoices', 'Api\InvoiceController@upload_invoices');
    Route::get('payable-invoice', 'Api\InvoiceController@payable_invoice');
    Route::resource('accounts', 'Api\AccountController', ['only' => ['index', 'show', 'store', 'update']]);
    Route::resource('payments', 'Api\PaymentController',['only' => ['index', 'store']]);
    Route::post('payment', 'Api\PaymentController@make_kp_payment');
    Route::get('account-types', 'Api\AccountController@account_types');
    Route::get('account-classes', 'Api\AccountController@account_classes');
    Route::resource('bank-transfers', 'Api\BankTransferController', ['only' => ['index', 'show', 'store']]);
    Route::resource('bank-transactions', 'Api\BankTransactionsController', ['only' => ['index', 'show', 'store', 'update']]);
});
