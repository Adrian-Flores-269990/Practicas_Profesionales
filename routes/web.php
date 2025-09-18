<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// HOME (raíz)
Route::view('/', 'welcome')->name('home');

// ADMIN y ALUMNO HOMES (igual que ya tenías)
Route::get('/admin/home', fn () => view('admin.dashboard'))->name('admin.home');
Route::get('/alumno/home', fn () => view('alumno.inicio'))->name('alumno.home');

// LOGINS (solo vistas GET)
Route::view('/alumno/login', 'auth.login')->name('alumno.login');
Route::view('/empleado/login', 'auth.loginEmpleado')->name('empleado.login');

// POST login (si tu formulario apunta a '/', puedes dejarlo igual)
Route::post('/', [AuthController::class, 'login'])->name('login');
