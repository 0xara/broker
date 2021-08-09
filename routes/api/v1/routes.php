<?php

Route::resource('/alert','Api\V1\User\ApiUserAlertController');

Route::resource('/broadcasting/subscribe','Api\V1\User\ApiUserBroadcastController@subscribed');
