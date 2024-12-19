<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MeetingController;

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

Route::get('/search', [MeetingController::class, 'index'])->name('meetings.search');
Route::post('/meetings', [MeetingController::class, 'show'])->name('meetings.show');
Route::get('/meetings', [MeetingController::class, 'show'])->name('meetings.show');

Route::get('/meetings/{meeting}/edit', [MeetingController::class, 'edit'])->name('meetings.edit');
Route::post('/meetings/{meeting}', [MeetingController::class, 'update'])->name('meetings.update');
