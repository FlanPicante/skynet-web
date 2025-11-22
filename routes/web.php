<?php

use App\Http\Controllers\AuthWebController;
use App\Http\Controllers\ClientsWebController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\UsersWebController;
use App\Http\Controllers\VisitsWebController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthWebController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');

Route::middleware('web_auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    //CLIENTES
    Route::get('/clients', [ClientsWebController::class, 'index'])->name('clients.index');
    Route::get('/clients/create', [ClientsWebController::class, 'create'])->name('clients.create');
    Route::post('/clients', [ClientsWebController::class, 'store'])->name('clients.store');
    Route::get('/clients/{id}/edit', [ClientsWebController::class, 'edit'])->name('clients.edit');
    Route::put('/clients/{id}', [ClientsWebController::class, 'update'])->name('clients.update');
    Route::get('/reportes/clientes', [ReportsController::class, 'clients'])->name('reports.clients');

    //VISITAS
    Route::get('/visits', [VisitsWebController::class, 'index'])->name('visits.index');
    Route::get('/visits/create', [VisitsWebController::class, 'create'])->name('visits.create');
    Route::post('/visits', [VisitsWebController::class, 'store'])->name('visits.store');
    Route::get('/visits/{id}/edit', [VisitsWebController::class, 'edit'])->name('visits.edit');
    Route::put('/visits/{id}', [VisitsWebController::class, 'update'])->name('visits.update');
    Route::get('/reportes/visits',[ReportsController::class,'visits'])->name('reports.visits');

    //USUARIOS
    Route::get('/users', [UsersWebController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UsersWebController::class, 'create'])->name('users.create');
    Route::post('/users', [UsersWebController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [UsersWebController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UsersWebController::class, 'update'])->name('users.update');

    Route::patch('/users/{id}/status', [UsersWebController::class, 'toggleStatus'])->name('users.status');


    Route::get('/users/{id}/password', [UsersWebController::class, 'editPassword'])->name('users.password.edit');
    Route::patch('/users/{id}/password', [UsersWebController::class, 'updatePassword'])->name('users.password.update');

    Route::get('/supervisors/{id}/technicians', [UsersWebController::class, 'editTechnicians'])->name('supervisors.technicians.edit');
    Route::post('/supervisors/{id}/technicians', [UsersWebController::class, 'updateTechnicians'])->name('supervisors.technicians.update');

    //VISITAS POR TECNICO
    Route::get('/mis-visitas-hoy', [VisitsWebController::class, 'today'])
        ->name('visits.today');

    Route::get('/visits/{id}', [VisitsWebController::class, 'show'])
        ->name('visits.show');

    Route::post('/visits/{id}/onroute', [VisitsWebController::class, 'markOnRoute'])
    ->name('visits.onroute');

    Route::post('/visits/{id}/checkin', [VisitsWebController::class, 'checkIn'])
        ->name('visits.checkin');

    Route::post('/visits/{id}/checkout', [VisitsWebController::class, 'checkOut'])
        ->name('visits.checkout');
});