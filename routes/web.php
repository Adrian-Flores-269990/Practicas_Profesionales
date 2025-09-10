<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

// Ruta de login
Route::get('', function () {
    return view('login');
});

// Ruta de inicio para el administrador
Route::get('/admin/home', function () {
    return view('homeAdmin');
})->name('admin.home');

// Ruta de inicio para el alumno
Route::get('/alumno/home', function () {
    return view('homeAlumno');
})->name('alumno.home');
