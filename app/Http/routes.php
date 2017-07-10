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

use Illuminate\Support\Facades\Route;

Route::options('/{any}', 'BaseController@options')->where(['any' => '.*']);

Route::get('/', 'ApiController@index');

Route::post('/authenticate', 'AuthController@authenticate');
Route::post('/validate-token', 'AuthController@validateToken');

Route::post('/tag/photos', 'TagsController@tagPhotos');
//Route::get('/tag/{tag}/photos', 'TagsController@getPhotosByTag');

//  /galleries
//  /galleries/:id
Route::resource('galleries', 'GalleriesController', array(
	'except' => array('create', 'edit')
));

//  /galleries/:id/photos
Route::resource('galleries.photos', "GalleryPhotosController", array(
	'except' => array('create', 'edit')
));
//  /galleries/:id/children

//  /photos
Route::resource('photos', 'PhotosController', array(
	'except' => array('create', 'edit')
));

//  /photos/:id/files
Route::resource('photos.files', 'PhotoFilesController', array(
	'except' => array('create', 'edit')
));

//  /photos/:id/image
Route::resource('photos.image', 'PhotoImageController', array(
	'except' => array('create', 'edit')
));
//  /photos/:id/metadata

// /tags
Route::resource('tags', 'TagsController', [
	'except' => ['create','edit']
]);

//  /tags/:name/photos
Route::resource('tags.photos', 'TagPhotosController', array(
    'except' => array('create', 'edit')
));

//  /user/login
//	/user/token
//	/user/token/refresh

