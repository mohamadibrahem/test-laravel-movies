<?php

Route::group(['namespace' => 'Api\V1\App', 'middleware' => ['auth:api']], function () {

    // Category
    Route::apiResource('categories', 'CategoryApiController');

    // Movie
    Route::post('movies/media', 'MovieApiController@storeMedia')->name('movies.storeMedia');
    Route::apiResource('movies', 'MovieApiController');
});

Route::group(['namespace' => 'Api\V1\App', 'as' => 'api.'], function () {
    Route::post('login', 'UsersApiController@login');
    Route::post('register', 'UsersApiController@register');

});