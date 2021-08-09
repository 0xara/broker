<?php

Route::resource('/alert','Api\V1\User\ApiUserAlertController');

Route::post('/broadcasting/subscribe','Api\V1\User\ApiUserBroadcastController@subscribed');
