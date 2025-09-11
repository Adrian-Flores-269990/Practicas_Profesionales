<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AuthController;

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

//Verificar datos
Route::post('/login', [AuthController::class, 'login'])->name('login');