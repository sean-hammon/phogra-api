<?php

namespace App\Http\Controllers;

use App\Phogra\Exception\BadRequestException;
use App\Phogra\Exception\NotFoundException;
use App\Phogra\Exception\InvalidOperationException;
use App\Phogra\Photo;
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
	 * @param  int|string $id integer row id for a single photo OR
	 *                          string of comma separated ids
	 * @return \Illuminate\Http\Response
	 * @throws \App\Phogra\Exception\BadRequestException
	 * @throws \App\Phogra\Exception\NotFoundException
	 */
	public function show($id)
	{
		if (is_numeric($id)) {

			//	This should be a single id
			$result = $this->repository->one($id, $this->requestParams);
		} else {

			//	Pull out all the commas. It should still be numeric.
			$quickcheck = str_replace(',', '', $id);
			if (!is_numeric($quickcheck)) {
				throw new BadRequestException('Non-numeric ids given. Spaces in your list? Or are you being naughty?');
			}

			$result = $this->repository->multiple($id, $this->requestParams);
		}

		if (is_null($result)) {
			throw new NotFoundException("photo {$id} does not exist.");
		} else {
			$response = new PhotosResponse($result);
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
