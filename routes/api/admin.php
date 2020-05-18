<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::group(
    [
        'prefix' => config('appconfig.admin_section', 'admin'),
        'namespace' => 'Api\Admin',
        'as' => 'admin.'
    ],
    function () {

        Route::post('/auth/login', 'Auth\AdminLoginController@login')
            ->name('login');

        Route::group(
            [
                'middleware' => 'auth:api:admin'
            ],
            function () {
                Route::resource('users', 'UserController');
            }
        );
    }
);
