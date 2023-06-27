<?php

use App\Http\Controllers\ToDoController;
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

Route::get('/', [ToDoController::class, 'index'])->middleware('auth');
Route::post('/create_to_do', [ToDoController::class, 'store'])->name('todos.store')->middleware('auth');
Route::put('/edit_to_do/{id}', [ToDoController::class, 'update'])->name('todos.update')->middleware('auth');
Route::get('/search_to_do/', [ToDoController::class, 'search'])->name('todos.search')->middleware('auth');
Route::delete('/delete_to_do/{id}', [TodoController::class, 'destroy'])->name('todos.destroy')->middleware('auth');

require __DIR__.'/auth.php';
