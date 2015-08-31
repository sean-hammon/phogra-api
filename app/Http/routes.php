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
Route::post('/authenticate', 'AuthController@authenticate');

Route::group(['middleware' => 'phogra.api.token'], function(){

	//  /galleries
	//  /galleries/:id
	Route::options('galleries', 'GalleriesController@options');
	Route::resource('galleries','GalleriesController', array(
		'except' => array('create','edit')
	));

	//  /galleries/:id/photos
	Route::resource('galleries.photos', "GalleryPhotosController", array(
        'except' => array('create','edit')
    ));
	//  /galleries/:id/children

	//  /photos
	Route::options('photos', 'PhotosController@options');
	Route::resource('photos','PhotosController', array(
		'except' => array('create','edit')
	));

	//  /photos/:id/files
	//  /photos/:id/metadata

	//  /user/login
	//	/user/token
	//	/user/token/refresh
});
