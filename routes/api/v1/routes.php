<?php

Route::resource('/alert','Api\V1\User\ApiUserAlertController');

Route::post('/broadcasting/subscribed','Api\V1\User\ApiUserBroadcastController@subscribed');
