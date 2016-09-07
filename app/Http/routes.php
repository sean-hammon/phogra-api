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

Route::get('/', 'ApiController@index');

Route::options('authenticate', 'AuthController@options');
Route::post('/authenticate', 'AuthController@authenticate');

Route::options('validate-token', 'AuthController@options');
Route::post('/validate-token', 'AuthController@validateToken');

//  /galleries
//  /galleries/:id
Route::options('galleries', 'GalleriesController@options');
Route::resource('galleries', 'GalleriesController', array(
	'except' => array('create', 'edit')
));

//  /galleries/:id/photos
Route::options('galleries.photos', 'GalleryPhotosController@options');
Route::resource('galleries.photos', "GalleryPhotosController", array(
	'except' => array('create', 'edit')
));
//  /galleries/:id/children

//  /photos
Route::options('photos', 'PhotosController@options');
Route::resource('photos', 'PhotosController', array(
	'except' => array('create', 'edit')
));

//  /photos/:id/files
Route::options('photos.files', 'PhotoFilesController@options');
Route::resource('photos.files', 'PhotoFilesController', array(
	'except' => array('create', 'edit')
));
//  /photos/:id/image
Route::options('photos.image', 'PhotoImageController@options');
Route::resource('photos.image', 'PhotoImageController', array(
	'except' => array('create', 'edit')
));
//  /photos/:id/metadata

//  /user/login
//	/user/token
//	/user/token/refresh
