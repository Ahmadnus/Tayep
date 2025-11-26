<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
use App\Events\TestEvent;

Route::get('/reverb-test', function () {
    return view('reverb-test');
});

Route::get('/broadcast-test', function () {
    // بث الحدث
    event(new TestEvent('Hello from Reverb!'));
    return 'Event broadcasted!';
});
Route::get('/listen', function () {
    // بث الحدث
 
    return view('listen');
});