<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});


//  /galleries
//  /galleries/:id
Route::resource('galleries','GalleriesController', array(
    'except' => array('create','edit')
));

//  /galleries/:id/photos
//  /galleries/:id/children

//  /photos
Route::resource('photos','PhotosController', array(
    'except' => array('create','edit')
));

//  /photos/:id/files
//  /photos/:id/metadata

//  /user/login
//	/user/token
//	/user/token/refresh
