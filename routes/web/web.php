<?php


Route::middleware('auth')->group(function () {
    //Route::resource('/alert','User\UserAlertController');
});

Auth::routes();
