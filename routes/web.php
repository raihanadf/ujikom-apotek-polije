<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin');
Route::redirect('/dashboard', '/admin');
Route::view('/register', '/register');
require __DIR__.'/auth.php';
