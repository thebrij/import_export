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

Route::get('/clear', function() {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    return "Cleared!";
});

Route::get('/', function () {
    if(Auth::check()) {
        return redirect()->route('home');
    }
    return view('welcome');
});
Route::get('/welcome', function () {
    if(Auth::check()) {
        return redirect()->route('home');
    }
    return view('welcome');
});
//Auth::routes();

// Authentication Routes...
Route::get ('test',  'Auth\LoginController@test')->name('test');
Route::get ('login',  'Auth\LoginController@showLoginForm')->name('login');
Route::post('login',  'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Registration Routes...
Route::get ('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');

// Password Reset Routes...
Route::get ('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get ('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/pageone', 'HomeController@pageone')->name('pageone');
Route::get('/pagetwo', 'HomeController@pagetwo')->name('pagetwo');

Route::group(['middleware' => 'auth'], function () {

	Route::resource('user'      , 'UserController'      , ['except' => ['show']])->middleware('role:Administrator|Manager|Supervisor');
    Route::resource('role'      , 'RoleController'      , ['except' => ['show']])->middleware('role:Administrator|Manager|Supervisor');
    Route::resource('dataaccess', 'DataAccessController', ['except' => ['show']]);
    Route::post('dataaccess/ajax_get_user_da_selyear'   , ['as' => 'dataaccess.ajax_get_user_da_selyear' , 'uses' => 'DataAccessController@ajax_get_user_da_selyear']);
    Route::post('dataaccess/ajax_get_uloads'            , ['as' => 'dataaccess.ajax_get_uloads'          , 'uses' => 'DataAccessController@ajax_get_uloads']);
    Route::post('dataaccess/ajax_get_cal_years'         , ['as' => 'dataaccess.ajax_get_cal_years'       , 'uses' => 'DataAccessController@ajax_get_cal_years']);
    Route::post('dataaccess/ajax_get_cal_months'        , ['as' => 'dataaccess.ajax_get_cal_months'      , 'uses' => 'DataAccessController@ajax_get_cal_months']);


    Route::get('profile',          ['as' => 'profile.edit',     'uses' => 'ProfileController@edit']);
	Route::put('profile',          ['as' => 'profile.update',   'uses' => 'ProfileController@update']);
    Route::put('profile/password', ['as' => 'profile.password', 'uses' => 'ProfileController@password']);
    Route::get('user/downloadall', ['as' => 'user.downloadall', 'uses' => 'UserController@downloadall']);

    Route::get('{page}', ['as' => 'page.index', 'uses' => 'PageController@index']);

    Route::get( 'importerbill/data_update',         ['as' => 'importerbill.updateImChartData',        'uses' => 'ImporterBillController@updateImChartData'])->middleware('role:Administrator|Manager|Supervisor');
    Route::get( 'importerbill/create',         ['as' => 'importerbill.create',        'uses' => 'ImporterBillController@create'])->middleware('role:Administrator|Manager|Supervisor');
    Route::post('importerbill/store',          ['as' => 'importerbill.store' ,        'uses' => 'ImporterBillController@store'])->middleware('role:Administrator|Manager|Supervisor');
    Route::get( 'importerbill/export',         ['as' => 'importerbill.export',        'uses' => 'ImporterBillController@export'])->middleware('role:Administrator|Manager|Supervisor');
    Route::post('importerbill/del_file_data',  ['as' => 'importerbill.del_file_data', 'uses' => 'ImporterBillController@del_file_data'])->middleware('role:Administrator|Manager|Supervisor');
    Route::get('importerbill-files/list',  ['as' => 'importerbill-files.list', 'uses' => 'ImporterBillController@importBillsFileList'])->middleware('role:Administrator|Manager|Supervisor');


    Route::post('importerbill/get_ajax_side_bar', ['as' => 'importerbill.get_ajax_side_bar', 'uses' => 'ImporterBillController@get_ajax_side_bar']);
    Route::post('importerbill/get_ajax', ['as' => 'importerbill.get_ajax', 'uses' => 'ImporterBillController@get_ajax']);
    Route::get('importerbill/ajax_importer_export', ['as' => 'importerbill.ajax_importer_export', 'uses' => 'ImporterBillController@ajax_importer_export']);

    Route::post('importerbill/get_ajax_top_usd',         ['as' => 'importerbill.get_ajax_top_usd',         'uses' => 'ImporterBillController@get_ajax_top_usd'])->middleware('role:Administrator|Manager|Supervisor|User');
    Route::post('importerbill/get_ajax_top_usd_port',    ['as' => 'importerbill.get_ajax_top_usd_port',    'uses' => 'ImporterBillController@get_ajax_top_usd_port'])->middleware('role:Administrator|Manager|Supervisor|User');
    Route::post('importerbill/get_ajax_top_usd_country', ['as' => 'importerbill.get_ajax_top_usd_country', 'uses' => 'ImporterBillController@get_ajax_top_usd_country'])->middleware('role:Administrator|Manager|Supervisor|User');

    Route::post('importerbill/get_ajax_impana_usd_comp',     ['as' => 'importerbill.get_ajax_impana_usd_comp',     'uses' => 'ImporterBillController@get_ajax_impana_usd_comp']);
    Route::post('importerbill/get_ajax_impana_usd_cost',     ['as' => 'importerbill.get_ajax_impana_usd_cost',     'uses' => 'ImporterBillController@get_ajax_impana_usd_cost']);
    Route::post('importerbill/get_ajax_impana_usd_quantity', ['as' => 'importerbill.get_ajax_impana_usd_quantity', 'uses' => 'ImporterBillController@get_ajax_impana_usd_quantity']);

    Route::post('importerbill/ga_imp_supana_usd_comp',     ['as' => 'importerbill.ga_imp_supana_usd_comp',     'uses' => 'ImporterBillController@ga_imp_supana_usd_comp']);
    Route::post('importerbill/ga_imp_supana_usd_cost',     ['as' => 'importerbill.ga_imp_supana_usd_cost',     'uses' => 'ImporterBillController@ga_imp_supana_usd_cost']);
    Route::post('importerbill/ga_imp_supana_usd_quantity', ['as' => 'importerbill.ga_imp_supana_usd_quantity', 'uses' => 'ImporterBillController@ga_imp_supana_usd_quantity']);


    Route::post('importerbill/ga_marketshare_cost_usd_port',    ['as' => 'importerbill.ga_marketshare_cost_usd_port',    'uses' => 'ImporterBillController@ga_marketshare_cost_usd_port']);
    Route::post('importerbill/ga_marketshare_cost_qua_port',    ['as' => 'importerbill.ga_marketshare_cost_qua_port',    'uses' => 'ImporterBillController@ga_marketshare_cost_qua_port']);
    Route::post('importerbill/ga_marketshare_cost_qua_country', ['as' => 'importerbill.ga_marketshare_cost_qua_country', 'uses' => 'ImporterBillController@ga_marketshare_cost_qua_country']);
    Route::post('importerbill/ga_marketshare_cost_usd_country', ['as' => 'importerbill.ga_marketshare_cost_usd_country', 'uses' => 'ImporterBillController@ga_marketshare_cost_usd_country']);

    Route::post('importerbill/ga_priceana_usd_country',  ['as' => 'importerbill.ga_priceana_usd_country',  'uses' => 'ImporterBillController@ga_priceana_usd_country']);
    Route::post('importerbill/ga_priceana_usd_port',     ['as' => 'importerbill.ga_priceana_usd_port',     'uses' => 'ImporterBillController@ga_priceana_usd_port']);
    Route::post('importerbill/ga_priceana_usd_importer', ['as' => 'importerbill.ga_priceana_usd_importer', 'uses' => 'ImporterBillController@ga_priceana_usd_importer']);

    Route::post('importerbill/ga_comparison_usd_importer', ['as' => 'importerbill.ga_comparison_usd_importer',  'uses' => 'ImporterBillController@ga_comparison_usd_importer']);
    Route::post('importerbill/ga_comparison_usd_country',  ['as' => 'importerbill.ga_comparison_usd_country',     'uses' => 'ImporterBillController@ga_comparison_usd_country']);
    Route::post('importerbill/ga_comparison_usd_ports',    ['as' => 'importerbill.ga_comparison_usd_ports', 'uses' => 'ImporterBillController@ga_comparison_usd_ports']);

    Route::post('importerbill/ga_gsum_8digit',  ['as' => 'importerbill.ga_gsum_8digit',  'uses' => 'ImporterBillController@ga_gsum_8digit']);
    Route::post('importerbill/ga_gsum_2digit',  ['as' => 'importerbill.ga_gsum_2digit',  'uses' => 'ImporterBillController@ga_gsum_2digit']);
    Route::post('importerbill/ga_gsum_4digit',  ['as' => 'importerbill.ga_gsum_4digit',  'uses' => 'ImporterBillController@ga_gsum_4digit']);
    Route::post('importerbill/ga_gsum_port',    ['as' => 'importerbill.ga_gsum_port',    'uses' => 'ImporterBillController@ga_gsum_port']);
    Route::post('importerbill/ga_gsum_country', ['as' => 'importerbill.ga_gsum_country', 'uses' => 'ImporterBillController@ga_gsum_country']);
    Route::post('importerbill/ga_gsum_unit',    ['as' => 'importerbill.ga_gsum_unit',    'uses' => 'ImporterBillController@ga_gsum_unit']);

    Route::post('importerbill/ga_pc_usd_country_max',    ['as' => 'importerbill.ga_pc_usd_country_max',    'uses' => 'ImporterBillController@ga_pc_usd_country_max']);
    Route::post('importerbill/ga_pc_qua_country_max',    ['as' => 'importerbill.ga_pc_qua_country_max',    'uses' => 'ImporterBillController@ga_pc_qua_country_max']);
    Route::post('importerbill/ga_pc_usd_country_min',    ['as' => 'importerbill.ga_pc_usd_country_min',    'uses' => 'ImporterBillController@ga_pc_usd_country_min']);
    Route::post('importerbill/ga_pc_qua_country_min',    ['as' => 'importerbill.ga_pc_qua_country_min',    'uses' => 'ImporterBillController@ga_pc_qua_country_min']);
    Route::post('importerbill/ga_pc_usd_port_max',    ['as' => 'importerbill.ga_pc_usd_port_max',    'uses' => 'ImporterBillController@ga_pc_usd_port_max']);
    Route::post('importerbill/ga_pc_qua_port_max',    ['as' => 'importerbill.ga_pc_qua_port_max',    'uses' => 'ImporterBillController@ga_pc_qua_port_max']);
    Route::post('importerbill/ga_pc_usd_port_min',    ['as' => 'importerbill.ga_pc_usd_port_min',    'uses' => 'ImporterBillController@ga_pc_usd_port_min']);
    Route::post('importerbill/ga_pc_qua_port_min',    ['as' => 'importerbill.ga_pc_qua_port_min',    'uses' => 'ImporterBillController@ga_pc_qua_port_min']);

    Route::post('importerbill/get_ajax_points_bal', ['as' => 'importerbill.get_ajax_points_bal', 'uses' => 'ImporterBillController@get_ajax_points_bal']);
    Route::post('importerbill/put_ajax_points',     ['as' => 'importerbill.put_ajax_points',     'uses' => 'ImporterBillController@put_ajax_points']);

    Route::get( 'exporterbill/update-charts',  ['as' => 'exporterbill.expUpdateChart','uses' => 'ChartsUpdateController@expUpdateChart'])->middleware('role:Administrator|Manager|Supervisor');
    Route::get( 'exporterbill/create',         ['as' => 'exporterbill.create',        'uses' => 'ExporterBillController@create'])->middleware('role:Administrator|Manager|Supervisor');
    Route::post('exporterbill/store',          ['as' => 'exporterbill.store' ,        'uses' => 'ExporterBillController@store'])->middleware('role:Administrator|Manager|Supervisor');
    Route::get( 'exporterbill/export',         ['as' => 'exporterbill.export',        'uses' => 'ExporterBillController@export'])->middleware('role:Administrator|Manager|Supervisor');
    Route::post( 'exporterbill/del_file_data', ['as' => 'exporterbill.del_file_data', 'uses' => 'ExporterBillController@del_file_data'])->middleware('role:Administrator|Manager|Supervisor');
    Route::get('exporterbill-files/list',  ['as' => 'exporterbill-files.list', 'uses' => 'ExporterBillController@expoterBillsFileList'])->middleware('role:Administrator|Manager|Supervisor');

    Route::post('exporterbill/get_ajax_side_bar',    ['as' => 'exporterbill.get_ajax_side_bar',    'uses' => 'ExporterBillController@get_ajax_side_bar']);
    Route::post('exporterbill/get_ajax',             ['as' => 'exporterbill.get_ajax',             'uses' => 'ExporterBillController@get_ajax']);
    Route::get('exporterbill/ajax_exporter_export',  ['as' => 'exporterbill.ajax_exporter_export', 'uses' => 'ExporterBillController@ajax_exporter_export']);

    Route::post('exporterbill/get_ajax_top_usd',         ['as' => 'exporterbill.get_ajax_top_usd',         'uses' => 'ExporterBillController@get_ajax_top_usd']);
    Route::post('exporterbill/get_ajax_top_usd_port',    ['as' => 'exporterbill.get_ajax_top_usd_port',    'uses' => 'ExporterBillController@get_ajax_top_usd_port']);
    Route::post('exporterbill/get_ajax_top_usd_country', ['as' => 'exporterbill.get_ajax_top_usd_country', 'uses' => 'ExporterBillController@get_ajax_top_usd_country']);

    Route::post('exporterbill/ga_exp_conana_usd_sup',      ['as' => 'exporterbill.ga_exp_conana_usd_sup',      'uses' => 'ExporterBillController@ga_exp_conana_usd_sup']);
    Route::post('exporterbill/ga_exp_conana_usd_cost',     ['as' => 'exporterbill.ga_exp_conana_usd_cost',     'uses' => 'ExporterBillController@ga_exp_conana_usd_cost']);
    Route::post('exporterbill/ga_exp_conana_usd_quantity', ['as' => 'exporterbill.ga_exp_conana_usd_quantity', 'uses' => 'ExporterBillController@ga_exp_conana_usd_quantity']);


    Route::post('exporterbill/get_ajax_expana_usd_sup',      ['as' => 'exporterbill.get_ajax_expana_usd_sup',      'uses' => 'ExporterBillController@get_ajax_expana_usd_sup']);
    Route::post('exporterbill/get_ajax_expana_usd_cost',     ['as' => 'exporterbill.get_ajax_expana_usd_cost',     'uses' => 'ExporterBillController@get_ajax_expana_usd_cost']);
    Route::post('exporterbill/get_ajax_expana_usd_quantity', ['as' => 'exporterbill.get_ajax_expana_usd_quantity', 'uses' => 'ExporterBillController@get_ajax_expana_usd_quantity']);

    Route::post('exporterbill/ga_marketshare_cost_usd_port',    ['as' => 'exporterbill.ga_marketshare_cost_usd_port',    'uses' => 'ExporterBillController@ga_marketshare_cost_usd_port']);
    Route::post('exporterbill/ga_marketshare_cost_qua_port',    ['as' => 'exporterbill.ga_marketshare_cost_qua_port',    'uses' => 'ExporterBillController@ga_marketshare_cost_qua_port']);
    Route::post('exporterbill/ga_marketshare_cost_qua_country', ['as' => 'exporterbill.ga_marketshare_cost_qua_country', 'uses' => 'ExporterBillController@ga_marketshare_cost_qua_country']);
    Route::post('exporterbill/ga_marketshare_cost_usd_country', ['as' => 'exporterbill.ga_marketshare_cost_usd_country', 'uses' => 'ExporterBillController@ga_marketshare_cost_usd_country']);

    Route::post('exporterbill/ga_priceana_usd_country',  ['as' => 'exporterbill.ga_priceana_usd_country',  'uses' => 'ExporterBillController@ga_priceana_usd_country']);
    Route::post('exporterbill/ga_priceana_usd_port',     ['as' => 'exporterbill.ga_priceana_usd_port',     'uses' => 'ExporterBillController@ga_priceana_usd_port']);
    Route::post('exporterbill/ga_priceana_usd_exporter', ['as' => 'exporterbill.ga_priceana_usd_exporter', 'uses' => 'ExporterBillController@ga_priceana_usd_exporter']);

    Route::post('exporterbill/ga_comparison_usd_exporter', ['as' => 'exporterbill.ga_comparison_usd_exporter', 'uses' => 'ExporterBillController@ga_comparison_usd_exporter']);
    Route::post('exporterbill/ga_comparison_usd_country',  ['as' => 'exporterbill.ga_comparison_usd_country',  'uses' => 'ExporterBillController@ga_comparison_usd_country']);
    Route::post('exporterbill/ga_comparison_usd_port',     ['as' => 'exporterbill.ga_comparison_usd_port',     'uses' => 'ExporterBillController@ga_comparison_usd_port']);

    Route::post('exporterbill/ga_gsum_8digit',  ['as' => 'exporterbill.ga_gsum_8digit',  'uses' => 'ExporterBillController@ga_gsum_8digit']);
    Route::post('exporterbill/ga_gsum_2digit',  ['as' => 'exporterbill.ga_gsum_2digit',  'uses' => 'ExporterBillController@ga_gsum_2digit']);
    Route::post('exporterbill/ga_gsum_4digit',  ['as' => 'exporterbill.ga_gsum_4digit',  'uses' => 'ExporterBillController@ga_gsum_4digit']);
    Route::post('exporterbill/ga_gsum_port',    ['as' => 'exporterbill.ga_gsum_port',    'uses' => 'ExporterBillController@ga_gsum_port']);
    Route::post('exporterbill/ga_gsum_country', ['as' => 'exporterbill.ga_gsum_country', 'uses' => 'ExporterBillController@ga_gsum_country']);
    Route::post('exporterbill/ga_gsum_unit',    ['as' => 'exporterbill.ga_gsum_unit',    'uses' => 'ExporterBillController@ga_gsum_unit']);

    Route::post('exporterbill/ga_pc_usd_country_max', ['as' => 'exporterbill.ga_pc_usd_country_max', 'uses' => 'ExporterBillController@ga_pc_usd_country_max']);
    Route::post('exporterbill/ga_pc_qua_country_max', ['as' => 'exporterbill.ga_pc_qua_country_max', 'uses' => 'ExporterBillController@ga_pc_qua_country_max']);
    Route::post('exporterbill/ga_pc_usd_country_min', ['as' => 'exporterbill.ga_pc_usd_country_min', 'uses' => 'ExporterBillController@ga_pc_usd_country_min']);
    Route::post('exporterbill/ga_pc_qua_country_min', ['as' => 'exporterbill.ga_pc_qua_country_min', 'uses' => 'ExporterBillController@ga_pc_qua_country_min']);
    Route::post('exporterbill/ga_pc_usd_port_max', ['as' => 'exporterbill.ga_pc_usd_port_max', 'uses' => 'ExporterBillController@ga_pc_usd_port_max']);
    Route::post('exporterbill/ga_pc_qua_port_max', ['as' => 'exporterbill.ga_pc_qua_port_max', 'uses' => 'ExporterBillController@ga_pc_qua_port_max']);
    Route::post('exporterbill/ga_pc_usd_port_min', ['as' => 'exporterbill.ga_pc_usd_port_min', 'uses' => 'ExporterBillController@ga_pc_usd_port_min']);
    Route::post('exporterbill/ga_pc_qua_port_min', ['as' => 'exporterbill.ga_pc_qua_port_min', 'uses' => 'ExporterBillController@ga_pc_qua_port_min']);


});

