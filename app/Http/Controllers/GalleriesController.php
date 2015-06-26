<?php

namespace App\Http\Controllers;

use App\Phogra\Gallery;
use App\Phogra\Response\Gallery as GalleryResponse;
use App\Phogra\Response\Galleries as GalleriesResponse;

class GalleriesController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$galleries = Gallery::all($this->requestParams);
		$content = new GalleriesResponse($galleries);
		return response()->json($content);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
