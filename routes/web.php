<?php

Route::get('/', function () {
    return view('welcome');
});

Route::get('/chat', function () {
    return view('chat');
});

Route::get('/privacy-policy', function () {
    return view('privacy-policy');
});

Route::get('/terms-of-service', function () {
    return view('terms-of-service');
});

Route::match(['get', 'post'], '/botman', 'BotManController@handle');
Route::get('/botman/tinker', 'BotManController@tinker');
