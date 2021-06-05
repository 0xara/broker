<?php

Route::post('/login', 'Api\V1\User\Auth\ApiLoginController');

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/user', 'Api\V1\User\ApiUserController');
    require base_path('routes/api/v1/routes.php');
});
