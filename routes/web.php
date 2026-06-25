<?php

use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\ClientReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\PipelineController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'role:admin,staff'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
    Route::get('/properties/create', [PropertyController::class, 'create'])->name('properties.create');
    Route::post('/properties', [PropertyController::class, 'store'])->name('properties.store');
    Route::get('/properties/{property:slug}', [PropertyController::class, 'show'])->name('properties.show');
    Route::get('/properties/{property:slug}/edit', [PropertyController::class, 'edit'])->name('properties.edit');
    Route::put('/properties/{property:slug}', [PropertyController::class, 'update'])->name('properties.update');
    Route::get('/properties/{property:slug}/report', [ClientReportController::class, 'show'])->name('properties.report');
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::get('/documents/create', [DocumentController::class, 'create'])->name('documents.create');
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{propertyLink}/edit', [DocumentController::class, 'edit'])->name('documents.edit');
    Route::put('/documents/{propertyLink}', [DocumentController::class, 'update'])->name('documents.update');
    Route::delete('/documents/{propertyLink}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    Route::get('/team', [TeamController::class, 'index'])->name('team.index');
    Route::get('/team/create', [TeamController::class, 'create'])->name('team.create');
    Route::post('/team', [TeamController::class, 'store'])->name('team.store');
    Route::get('/team/{teamMember}/edit', [TeamController::class, 'edit'])->name('team.edit');
    Route::put('/team/{teamMember}', [TeamController::class, 'update'])->name('team.update');
    Route::delete('/team/{teamMember}', [TeamController::class, 'destroy'])->name('team.destroy');
    Route::get('/marketing', [MarketingController::class, 'index'])->name('marketing.index');
    Route::get('/marketing/create', [MarketingController::class, 'create'])->name('marketing.create');
    Route::post('/marketing', [MarketingController::class, 'store'])->name('marketing.store');
    Route::get('/marketing/{marketingActivity}/edit', [MarketingController::class, 'edit'])->name('marketing.edit');
    Route::put('/marketing/{marketingActivity}', [MarketingController::class, 'update'])->name('marketing.update');
    Route::delete('/marketing/{marketingActivity}', [MarketingController::class, 'destroy'])->name('marketing.destroy');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{property:slug}', [ReportController::class, 'show'])->name('reports.show');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/pipeline', [PipelineController::class, 'index'])->name('pipeline.index');
    Route::get('/pipeline/create', [PipelineController::class, 'create'])->name('pipeline.create');
    Route::post('/pipeline', [PipelineController::class, 'store'])->name('pipeline.store');
    Route::get('/pipeline/{prospect}/edit', [PipelineController::class, 'edit'])->name('pipeline.edit');
    Route::put('/pipeline/{prospect}', [PipelineController::class, 'update'])->name('pipeline.update');
});

Route::middleware(['auth', 'role:client'])->prefix('client')->name('client.')->group(function () {
    Route::get('/properties', [ClientReportController::class, 'index'])->name('properties');
    Route::get('/properties/{property:slug}', [ClientReportController::class, 'show'])->name('properties.show');
});
