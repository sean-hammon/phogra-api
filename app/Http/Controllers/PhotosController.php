<?php

namespace App\Http\Controllers;

use App\Phogra\Exception\BadRequestException;
use App\Phogra\Exception\InvalidOperationException;
use App\Phogra\Photo;
use App\Phogra\Response\Photo as PhotoResponse;
use App\Phogra\Response\Photos as PhotosResponse;
use Illuminate\Http\Request;

class PhotosController extends BaseController {

	private $repository;

	public function __construct(Request $request, Photo $repository) {
		parent::__construct($request);
		$this->repository = $repository;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @throws InvalidOperationException
	 */
	public function index()
	{
		throw new InvalidOperationException('Retrieving all photos is not supported.');
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
	 * Display the specified photo(s).
	 *
	 * @param  int|string  $id  integer row id for a single photo OR
	 *                          string of comma separated ids
	 *
	 * @return Response
	 * @throws BadRequestException
	 */
	public function show($id)
	{
		if (is_numeric($id)) {

			//	This should be a single id
			$photo = $this->repository->one($id, $this->requestParams);
			$response = new PhotosResponse($photo);

			return $response->send();
		} else {

			//	Pull out all the commas. It should still be numeric.
			$quickcheck = str_replace(',', '', $id);
			if (!is_numeric($quickcheck)) {
				throw new BadRequestException('Non-numeric ids given. Spaces in your list?');
			}

			$photos = $this->repository->multiple($id, $this->requestParams);

			$response = new PhotosResponse($photos);
			return $response->send();
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
