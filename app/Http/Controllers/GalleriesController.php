<?php

namespace App\Http\Controllers;

use App\Phogra\Exception\BadRequestException;
use App\Phogra\Gallery;
use App\Phogra\Response\Gallery as GalleryResponse;
use App\Phogra\Response\Galleries as GalleriesResponse;
use Illuminate\Http\Request;

class GalleriesController extends BaseController {

	private $repository;

	public function __construct(Request $request, Gallery $repository) {
		parent::__construct($request);
		$this->repository = $repository;
	}

	/**
	 * Return all gallery records
	 *
	 * @return \App\Phogra\Response
	 */
	public function index()
	{
		$galleries = $this->repository->all($this->requestParams);

		$response = new GalleriesResponse($galleries);
		return $response->send();
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
	 * Display the specified galleries.
	 *
	 * @param  int|string  $id  integer row id for a single gallery OR
	 *                          string of comma separated ids
	 *
	 * @return Response
	 * @throws BadRequestException
	 */
	public function show($id)
	{
		if (is_numeric($id)) {

			//	This should be a single id
			$gallery = $this->repository->one($id, $this->requestParams);

			$content = new GalleryResponse($gallery);
			return response()->json($content);
		} else {

			//	Pull out all the commas. It should still be numeric.
			$quickcheck = str_replace(',', '', $id);
			if (!is_numeric($quickcheck)) {
				throw new BadRequestException('Non-numeric ids given. Spaces in your list?');
			}

			$galleries = $this->repository->multiple($id, $this->requestParams);

			$content = new GalleriesResponse($galleries);
			return response()->json($content);
		}
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
