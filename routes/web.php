<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// Post Transaction routes
Route::post('/payment', 'PaymentController@index')->name('payment')->middleware('check.client');
Route::post('/payment/status', 'RequestController@status')->name('payment.status')->middleware('check.client');
Route::post('/payment/verify', 'RequestController@verify')->name('payment.verify')->middleware('check.client');
Route::post('/payment/retry', 'ProcessRequestController@retry')->name('payment.retry')->middleware('check.client');


Route::get('/app/clients', 'HomeController@appClients')->name('app.clients');
Route::get('/app/update/clients', 'HomeController@updateAppClients')->name('app.update.client');

Route::get('/pay', 'RequestController@index')->name('pay');
Route::get('/process/{txnId}', 'ProcessRequestController@index')->name('process');
Route::get('/process/{txnId}/bank/{optionId}', 'ProcessRequestController@selectbank')->name('process.bank');

# Process BKash form
Route::get('/process/bkash/{txnId}', 'ProcessRequestController@bkashForm')->name('process.bkash');
Route::post('/process/bkash/{txnId}', 'ProcessRequestController@bkashFormSubmit')->name('process.bkashSubmit');
Route::get('/process/bkash/checkout/{txnId}', 'BkashCheckoutController@process')->name('process.bkashCheckout');
Route::get('/complete/bkash/checkout/{txnId}', 'BkashCheckoutController@completePayment')->name('complete.bkashCheckout');

# Process surecash form
Route::get('/process/surecash/{txnId}', 'ProcessRequestController@surecashForm')->name('process.surecash');
Route::post('/process/surecash/{txnId}', 'ProcessRequestController@surecashFormSubmit')->name('process.surecashSubmit');

# Process DBBL request
Route::post('/process/dbbl/success', 'ProcessRequestController@dbblSuccess')->name('process.dbbl.success');
Route::post('/process/dbbl/fail', 'ProcessRequestController@dbblFail')->name('process.dbbl.fail');
Route::post('/callback/dbbl/success', 'ProcessRequestController@dbblSuccess')->name('process.dbbl.successnew');
Route::post('/callback/dbbl/fail', 'ProcessRequestController@dbblFail')->name('process.dbbl.failnew');

# Process CityBank request
Route::get('/test/citybank', 'ProcessRequestController@testCityBankSocket')->name('test.citybank');
Route::post('/callback/citybank/success', 'ProcessRequestController@cityBankSuccess')->name('callback.citybank.success');
Route::post('/callback/citybank/cancel', 'ProcessRequestController@cityBankCancel')->name('callback.citybank.cancel');
Route::post('/callback/citybank/fail', 'ProcessRequestController@cityBankFail')->name('callback.citybank.fail');

# Process EBLBank request
Route::get('/callback/eblbank/success', 'ProcessRequestController@eblBankSuccess')->name('callback.eblbank.success');
Route::get('/callback/eblbank/cancel', 'ProcessRequestController@eblBankCancel')->name('callback.eblbank.cancel');
Route::get('/callback/eblbank/fail', 'ProcessRequestController@eblBankFail')->name('callback.eblbank.fail');

Route::post('/callback/{bid}/{bank_request_id}', 'CallbackController@index')->name('callback');

Route::group(['middleware' => ['auth']], function()
{
    Route::get('/transactions', 'HomeController@transactions')->name('transactions');
    Route::get('/transactions/pdf/{id}', 'HomeController@getTransactionsPDF')->name('transactions.pdf');
    Route::get('/transactions/edit', 'HomeController@editTransactions')->name('transactions.edit');
    Route::post('/transactions/edit', 'HomeController@updateTransactions')->name('transactions.update');
    Route::get('/bankRequests', 'HomeController@bankRequests')->name('bankRequests');
    Route::get('/merchantRequestDetails', 'HomeController@merchantRequestDetails')->name('merchantRequestDetails');
    //USER RELATED ROUTES
    Route::get('/users', 'UserController@index')->name('users')->middleware('can:index,App\User');
    Route::get('/users/index', 'UserController@index')->name('user.index')->middleware('can:index,App\User');
    Route::get('/users/create', 'UserController@create')->name('user.create')->middleware('can:create,App\User');
    Route::post('/users/store', 'UserController@store')->name('user.store')->middleware('can:create,App\User');
    Route::get('/users/show/{user}', 'UserController@show')->name('user.show')->middleware('can:view,user');

    Route::get('/users/updatePassword/{user}', 'UserController@updatePasswordPage')->name('user.updatePassword')->middleware('can:update,user');
    Route::get('/users/updateStatus/{user}', 'UserController@updateStatus')->name('user.updateStatus')->middleware('can:update,user');
    Route::get('/users/updateEmail/{user}', 'UserController@updateEmailPage')->name('user.updateEmail')->middleware('can:update,user');
    Route::get('/users/updatePhone/{user}', 'UserController@updatePhonePage')->name('user.updatePhone')->middleware('can:update,user');
    Route::get('/users/updateRole/{user}', 'UserController@updateRolePage')->name('user.updateRole')->middleware('can:update,user');
    Route::get('/users/updateBankIds/{user}', 'UserController@updateBankIdsPage')->name('user.updateBankIds')->middleware('can:update,user');
    Route::get('/users/updateInvoiceSettings/{user}', 'UserController@updateInvoiceSettingsPage')->name('user.updateInvoiceSettings')->middleware('can:update,user');


    Route::post('/users/updatePassword/{user}', 'UserController@storeUpdatedPassword')->name('user.storeUpdatedPassword')->middleware('can:update,user');
    Route::post('/users/updateStatus/{user}', 'UserController@storeUpdatedStatus')->name('user.storeUpdatedStatus')->middleware('can:update,user');
    Route::post('/users/updateEmail/{user}', 'UserController@storeUpdatedEmail')->name('user.storeUpdatedEmail')->middleware('can:update,user');
    Route::post('/users/updatePhone/{user}', 'UserController@storeUpdatedPhone')->name('user.storeUpdatedPhone')->middleware('can:update,user');
    Route::post('/users/updatedBankIds/{user}', 'UserController@storeUpdatedBankIds')->name('user.storeUpdatedBankIds')->middleware('can:update,user');
    Route::post('/users/updateInvoiceSettings/{user}', 'UserController@storeUpdatedInvoiceSettings')->name('user.storeUpdateInvoiceSettings')->middleware('can:update,user');
    Route::post('/users/updateRole/{user}', 'UserController@storeUpdatedRole')->name('user.storeUpdatedRole')->middleware('can:update,user');
    Route::get('/users/delete/{user}', 'UserController@delete')->name('user.delete')->middleware('can:delete,user');
    Route::post('/users/confirmDelete', 'UserController@destroy')->name('user.confirmDelete')->middleware('can:delete,App\User');

    Route::get('/banks', 'BankController@index')->name('banks')->middleware('can:index,App\Bank');
    Route::get('/banks/index', 'BankController@index')->name('banks.index')->middleware('can:index,App\Bank');
    Route::get('/banks/create', 'BankController@create')->name('banks.create')->middleware('can:create,App\Bank');
    Route::post('/banks/store', 'BankController@store')->name('banks.store')->middleware('can:create,App\Bank');
    Route::get('/banks/show/{bank}', 'BankController@show')->name('banks.show')->middleware('can:view,bank');
    Route::get('/banks/edit/{bank}', 'BankController@edit')->name('banks.edit')->middleware('can:update,bank');
    Route::post('/banks/update/{bank}', 'BankController@update')->name('banks.update')->middleware('can:update,bank');
    Route::get('/banks/delete/{bank}', 'BankController@delete')->name('banks.delete')->middleware('can:delete,bank');
    Route::post('/banks/delete/{bank}', 'BankController@destroy')->name('banks.destroy')->middleware('can:delete,bank');

    Route::get('/paymentOptions', 'PaymentOptionController@index')->name('paymentOptions')->middleware('can:index,App\PaymentOption');
    Route::get('/paymentOptions/index', 'PaymentOptionController@index')->name('paymentOptions.index')->middleware('can:index,App\PaymentOption');
    Route::get('/paymentOptions/create', 'PaymentOptionController@create')->name('paymentOptions.create')->middleware('can:create,App\PaymentOption');
    Route::post('/paymentOptions/store', 'PaymentOptionController@store')->name('paymentOptions.store')->middleware('can:create,App\PaymentOption');
    Route::get('/paymentOptions/show/{paymentOption}', 'PaymentOptionController@show')->name('paymentOptions.show')->middleware('can:view,paymentOption');
    Route::get('/paymentOptions/edit/{paymentOption}', 'PaymentOptionController@edit')->name('paymentOptions.edit')->middleware('can:update,paymentOption');
    Route::post('/paymentOptions/update/{paymentOption}', 'PaymentOptionController@update')->name('paymentOptions.update')->middleware('can:update,paymentOption');
    Route::get('/paymentOptions/delete/{paymentOption}', 'PaymentOptionController@delete')->name('paymentOptions.delete')->middleware('can:delete,paymentOption');
    Route::post('/paymentOptions/delete/{paymentOption}', 'PaymentOptionController@destroy')->name('paymentOptions.destroy')->middleware('can:delete,paymentOption');

    Route::get('/paymentOptionRates', 'PaymentOptionRateController@index')->name('paymentOptionRates')->middleware('can:index,App\PaymentOptionRate');
    Route::get('/paymentOptionRates/index', 'PaymentOptionRateController@index')->name('paymentOptionRates.index')->middleware('can:index,App\PaymentOptionRate');
    Route::get('/paymentOptionRates/create', 'PaymentOptionRateController@create')->name('paymentOptionRates.create')->middleware('can:create,App\PaymentOptionRate');
    Route::post('/paymentOptionRates/store', 'PaymentOptionRateController@store')->name('paymentOptionRates.store')->middleware('can:create,App\PaymentOptionRate');
    Route::get('/paymentOptionRates/show/{client_id}', 'PaymentOptionRateController@show')->name('paymentOptionRates.show')/*->middleware('can:edit,paymentOptionRate')*/;
//    Route::get('/paymentOptionRates/edit/{paymentOptionRate}', 'PaymentOptionRateController@edit')->name('paymentOptionRates.edit')->middleware('can:update,paymentOptionRate');
    Route::post('/paymentOptionRates/update/{paymentOptionRate}', 'PaymentOptionRateController@update')->name('paymentOptionRates.update')->middleware('can:update,paymentOptionRate');
//    Route::get('/paymentOptionRates/delete/{paymentOptionRate}', 'PaymentOptionRateController@delete')->name('paymentOptionRates.delete')->middleware('can:delete,paymentOptionRate');
    Route::post('/paymentOptionRates/delete/{paymentOptionRate}', 'PaymentOptionRateController@destroy')->name('paymentOptionRates.destroy')->middleware('can:delete,paymentOptionRate');

    Route::get('/withdraws', 'WithdrawController@index')->name('withdraws')->middleware('can:index,App\Bank');
    Route::get('/withdraws/index', 'WithdrawController@index')->name('withdraws.index')->middleware('can:index,App\Bank');
    Route::get('/withdraws/create', 'WithdrawController@create')->name('withdraws.create')->middleware('can:create,App\Bank');
    Route::post('/withdraws/store', 'WithdrawController@store')->name('withdraws.store')->middleware('can:create,App\Bank');
    Route::get('/withdraws/show/{bank}', 'WithdrawController@show')->name('withdraws.show')->middleware('can:view,bank');
    Route::get('/withdraws/edit/{bank}', 'WithdrawController@edit')->name('withdraws.edit')->middleware('can:update,bank');
    Route::post('/withdraws/update/{bank}', 'WithdrawController@update')->name('withdraws.update')->middleware('can:update,bank');
    Route::get('/withdraws/delete/{withdraw}', 'WithdrawController@delete')->name('withdraws.delete')->middleware('can:delete,withdraw');
    Route::post('/withdraws/delete/{bank}', 'WithdrawController@destroy')->name('withdraws.destroy')->middleware('can:delete,withdraw');
    Route::post('/withdraws/ajax-withdraw-data', 'WithdrawController@ajaxWithdrawData')->name('withdraws.ajaxWithdrawData')->middleware('can:create,App\Bank');

    Route::get('/withdraws/report', 'WithdrawController@report')->name('withdraws.report')->middleware('can:index,App\Bank');
    Route::get('/reports', 'ReportController@index')->name('reports')->middleware('can:index,App\Bank');
    Route::post('/reports', 'ReportController@index')->name('reports')->middleware('can:index,App\Bank');

    Route::get('/withdraw/request', 'WithdrawRequestController@index')->name('withdraw.request');
    Route::post('/withdraw/request', 'WithdrawRequestController@create')->name('withdraw.request');
    Route::post('/withdraw/request/submit', 'WithdrawRequestController@store')->name('withdraw.request.store');
    Route::get('/withdraw/request/list', 'WithdrawRequestController@show')->name('withdraw.request.list');
    Route::get('/withdraw/request/list/details/{id}', 'WithdrawRequestController@edit')->name('withdraw.request.edit');
    Route::post('/withdraw/request/list/update', 'WithdrawRequestController@update')->name('withdraw.request.update');
    Route::post('/withdraw/request/paid/{withdrawID}', 'WithdrawRequestController@ajaxWithdrawPaidRequest')->name('withdraw.request.paid');
    Route::get('/withdraw/request/report', 'WithdrawRequestController@WithdrawReport')->name('withdraw.report');
    Route::post('/withdraw/request/report', 'WithdrawRequestController@WithdrawShow')->name('withdraw.report');


});

Route::get('/test-email', 'TestController@testEmail')->name('test.email');
