<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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



Route::group(['middleware' => ['auth','admin']], function() {
    // ----------- For admin & developer ----------
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class,'dashboard'] );
    Route::resource('/users', \App\Http\Controllers\Admin\UserController::class );
    Route::resource('/quizzes', \App\Http\Controllers\Admin\QuizController::class );
});

//Route::group(['middleware' => ['auth']], function() {
//    // ----------- For General user ----------
//    Route::get('/user-dashboard', [\App\Http\Controllers\Client\DashboardController::class,'dashboard'] );
//    Route::resource('/user-quiz', \App\Http\Controllers\Client\QuizController::class );
//    Route::get('/user-quiz-answer', [\App\Http\Controllers\Client\QuizAnswerController::class,'index'] );
//});

Route::get('/', function (){
    return redirect('/login');
});

//Route::middleware([
//    'auth:sanctum',
//    config('jetstream.auth_session'),
//    'verified',
//])->group(function () {
//    Route::get('/dashboard', function () {
//        return Inertia::render('Dashboard');
//    })->name('dashboard');
//});
