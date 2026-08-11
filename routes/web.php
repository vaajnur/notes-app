<?php

use Illuminate\Support\Facades\Route;

Route::view('/api/docs', 'swagger')->name('api.docs');
Route::view('/{path?}', 'app')->where('path', '^(?!api).*$');
