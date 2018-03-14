<?php

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

Route::get('/', function () {
    return view('welcome');
})->name('login');

Route::post('/login', 'Auth\LoginController@login');
Route::post('/logout', 'Auth\LoginController@logout');

// Auth::routes();

Route::get('/dashboard', 'HomeController@index')->name('home');
Route::post('/save-assigned-users', 'HomeController@saveAssignedUsersForProject');



/**
 *  Routes for all projects and log operations
 */
Route::post('/projects', 'HomeController@projects');
Route::post('/log-project', 'HomeController@saveLog');
Route::post('/update-log', 'HomeController@updateLog');
Route::post('/delete-log', 'HomeController@deleteLog');
Route::post('/add-project', 'HomeController@addProject');
Route::post('/save-admin-log-review', 'HomeController@saveAdminLogReview');

/**
 *  Routes for all profile operations
 */
Route::post('/update-profile', 'HomeController@updateProfile');

/**
 *  Routes for all user record management operations
 */
Route::post('/users', 'HomeController@getUsers');
Route::post('/register-user', 'HomeController@registerUser');
Route::post('/delete-user', 'HomeController@deleteUser');
Route::post('/update-user-record', 'HomeController@adminUpdateUserRecord');