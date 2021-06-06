<?php

use App\Http\Controllers\User\UserAlertController;
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

Route::get('/home', 'HomeController@index')->name('home');

Route::group([], function (){
    require base_path('routes/web/web.php');
});

Route::get('/{any?}', function () {
    return view('user.index');
})
    ->where('any', '.*');
