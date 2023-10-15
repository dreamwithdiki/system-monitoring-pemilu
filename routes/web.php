<?php

use Illuminate\Support\Facades\Route;

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

$controller_path = 'App\Http\Controllers';

Route::get('/login', $controller_path . '\auth\LoginController@index')->name('auth-login');
Route::post('/login', $controller_path . '\auth\LoginController@doLogin')->name('auth-do-login');
Route::get('/logout', $controller_path . '\auth\LoginController@logout')->name('auth-logout');
Route::get('/forgot-password', $controller_path . '\auth\LoginController@forgot_password')->name('auth-login-forgot_password');
Route::post('/reset-password', $controller_path . '\auth\LoginController@reset_password')->name('auth-login-reset_password');

Route::group(['middleware' => 'check.tokey'], function () use ($controller_path) {
    Route::get('/', $controller_path . '\dashboard\DashboardController@index')->name('dashboard');
    Route::get('/dashboard', $controller_path . '\dashboard\DashboardController@index')->name('dashboard-index');
    Route::get('/filter-year-month/{year}/{month}', $controller_path . '\dashboard\DashboardController@filter_year_month');
    Route::post('/change-password', $controller_path . '\auth\LoginController@change_password')->name('change-password');

    // Master Data Autocomplete
    Route::prefix('autocomplete')->group(function () use ($controller_path) {
        Route::get('role/find', $controller_path . '\settings\RoleController@find');
        Route::get('role/find-saksi', $controller_path . '\settings\RoleController@find_saksi');
        Route::get('tps/find', $controller_path . '\pemilu\master_data\DataTpsController@find');
    });

    // MASTER
    Route::prefix('pemilu')->group(function () use ($controller_path) {
        $path_pemilu = $controller_path . '\pemilu';

        // Master Data
        Route::prefix('master-data')->group(function () use ($path_pemilu) {
            $path_pemilu_master_data = $path_pemilu . '\master_data';

            // Data Caleg
            Route::prefix('caleg')->group(function () use ($path_pemilu_master_data) {
                Route::get('/', $path_pemilu_master_data . '\DataCalegController@index')->name('pemilu-master-data-caleg');
                Route::get('get', $path_pemilu_master_data . '\DataCalegController@datatable');
                Route::get('show/{id}', $path_pemilu_master_data . '\DataCalegController@show');
                // menampilkan data ke select2
                Route::get('get-provinces', $path_pemilu_master_data . '\DataCalegController@getProvinces');
                Route::get('get-regencies', $path_pemilu_master_data . '\DataCalegController@getRegencies');
                Route::get('get-districts', $path_pemilu_master_data . '\DataCalegController@getDistricts');
                Route::get('get-villages', $path_pemilu_master_data . '\DataCalegController@getVillages');
                // end menampilkan data ke select2
                Route::post('store', $path_pemilu_master_data . '\DataCalegController@store');
                Route::post('update/{id}', $path_pemilu_master_data . '\DataCalegController@update');
                Route::post('update-status', $path_pemilu_master_data . '\DataCalegController@statusUpdate');
                Route::get('uploads/{caleg_id}', $path_pemilu_master_data . '\DataCalegController@show_upload_caleg');
                Route::post('delete', $path_pemilu_master_data . '\DataCalegController@delete');
            });

            // Data User
            Route::prefix('user')->group(function () use ($path_pemilu_master_data) {
                Route::get('/', $path_pemilu_master_data . '\UserController@index')->name('pemilu-master-data-user');
                Route::get('get', $path_pemilu_master_data . '\UserController@datatable');
                Route::get('show/{id}', $path_pemilu_master_data . '\UserController@show');
                // menampilkan data ke select2
                Route::get('get-provinces', $path_pemilu_master_data . '\UserController@getProvinces');
                Route::get('get-regencies', $path_pemilu_master_data . '\UserController@getRegencies');
                Route::get('get-districts', $path_pemilu_master_data . '\UserController@getDistricts');
                Route::get('get-villages', $path_pemilu_master_data . '\UserController@getVillages');
                // end menampilkan data ke select2
                Route::get('uploads/{user_id}', $path_pemilu_master_data . '\UserController@show_upload_user');
                Route::post('store', $path_pemilu_master_data . '\UserController@store');
                Route::post('update/{id}', $path_pemilu_master_data . '\UserController@update');
                Route::post('update-status', $path_pemilu_master_data . '\UserController@update_status');
                Route::post('delete', $path_pemilu_master_data . '\UserController@delete');
                // user only
                Route::post('update-user/{id}', $path_pemilu_master_data . '\UserController@update_data_user');
            });

            // Data TPS
            Route::prefix('tps')->group(function () use ($path_pemilu_master_data) {
                Route::get('/', $path_pemilu_master_data . '\DataTpsController@index')->name('pemilu-master-data-tps');
                Route::get('get', $path_pemilu_master_data . '\DataTpsController@datatable');
                Route::get('show/{id}', $path_pemilu_master_data . '\DataTpsController@show');
                // menampilkan data ke select2
                Route::get('get-provinces', $path_pemilu_master_data . '\DataTpsController@getProvinces');
                Route::get('get-regencies', $path_pemilu_master_data . '\DataTpsController@getRegencies');
                Route::get('get-districts', $path_pemilu_master_data . '\DataTpsController@getDistricts');
                Route::get('get-villages', $path_pemilu_master_data . '\DataTpsController@getVillages');
                // end menampilkan data ke select2
                Route::get('uploads/{tps_id}', $path_pemilu_master_data . '\DataTpsController@show_upload_tps');
                Route::post('store', $path_pemilu_master_data . '\DataTpsController@store');
                Route::post('update/{id}', $path_pemilu_master_data . '\DataTpsController@update');
                Route::post('update-status', $path_pemilu_master_data . '\DataTpsController@statusUpdate');
                Route::post('delete', $path_pemilu_master_data . '\DataTpsController@delete');
            });

        });

    });

    // DATA PENDUKUNG
    Route::prefix('pendukung')->group(function () use ($controller_path) {
        $path_pendukung = $controller_path . '\pendukung';
        // DPT
        Route::prefix('dpt')->group(function () use ($path_pendukung) {
            Route::get('/', $path_pendukung . '\DataDptController@index')->name('dpt');
            Route::get('get', $path_pendukung . '\DataDptController@datatable');
            Route::get('show/{id}', $path_pendukung . '\DataDptController@show');
            // menampilkan data ke select2
            Route::get('get-provinces', $path_pendukung . '\DataDptController@getProvinces');
            Route::get('get-regencies', $path_pendukung . '\DataDptController@getRegencies');
            Route::get('get-districts', $path_pendukung . '\DataDptController@getDistricts');
            Route::get('get-villages', $path_pendukung . '\DataDptController@getVillages');
            // end menampilkan data ke select2
            Route::post('store', $path_pendukung . '\DataDptController@store');
            Route::post('update/{id}', $path_pendukung . '\DataDptController@update');
            Route::post('update-status', $path_pendukung . '\DataDptController@statusUpdate');
            Route::post('delete', $path_pendukung . '\DataDptController@delete');
        });

    });

    // Settings
    Route::prefix('settings')->group(function () use ($controller_path) {
        $path_settings = $controller_path . '\settings';

        // User
        Route::prefix('user')->group(function () use ($path_settings) {
            Route::get('/', $path_settings . '\UserController@index')->name('settings-user');
            Route::get('get', $path_settings . '\UserController@datatable');
            Route::get('show/{id}', $path_settings . '\UserController@show');
            Route::get('uploads/{user_id}', $path_settings . '\UserController@show_upload_user');
            Route::post('store', $path_settings . '\UserController@store');
            Route::post('update/{id}', $path_settings . '\UserController@update');
            Route::post('update-status', $path_settings . '\UserController@update_status');
            Route::post('delete', $path_settings . '\UserController@delete');
            // user only
            Route::post('update-user/{id}', $path_settings . '\UserController@update_data_user');
        });
    });

    // Report
    Route::prefix('report')->group(function () use ($controller_path) {
        $path_partner = $controller_path . '\report';

        // Visit Order
        Route::prefix('report-visit-order')->group(function () use ($path_partner) {
            Route::get('/', $path_partner . '\ReportVisitOrderController@index')->name('report-visit-order');
            Route::get('get', $path_partner . '\ReportVisitOrderController@datatable');
            Route::post('set-download', $path_partner . '\ReportVisitOrderController@setDownload');
            Route::get('findClient', $path_partner . '\ReportVisitOrderController@findClient');
            Route::get('findSite', $path_partner . '\ReportVisitOrderController@findSite');
            Route::get('findPartner', $path_partner . '\ReportVisitOrderController@findPartner');
            Route::get('show/{id}', $path_partner . '\ReportVisitOrderController@show');
            Route::get('pdf/{id}', $path_partner . '\ReportVisitOrderController@pdf');
            Route::get('excel/{id}', $path_partner . '\ReportVisitOrderController@excel');
            Route::get('data-excel', $path_partner . '\ReportVisitOrderController@dataExcel');
        });
    });
});
