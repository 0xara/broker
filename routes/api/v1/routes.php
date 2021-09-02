<?php

Route::get('/exchange','Api\V1\User\ApiUserExchangeController@index');
Route::get('/exchange/symbolsList','Api\V1\User\ApiUserExchangeController@symbolsSelectBoxList');
Route::resource('/alert','Api\V1\User\ApiUserAlertController');
Route::resource('/watchlist','Api\V1\User\ApiUserWatchlistController');
Route::resource('/watchlistItem','Api\V1\User\ApiUserWatchlistItemController');
