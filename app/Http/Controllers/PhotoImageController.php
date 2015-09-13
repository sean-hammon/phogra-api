<?php

namespace app\Http\Controllers;

use App\Phogra\Exception\BadRequestException;
use App\Phogra\Exception\NotFoundException;

class PhotoImageController extends ApiController
{
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

	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 * @throws InvalidJsonException
	 */
	public function store()
	{

	}

	/**
	 * Retrieve the specified file(s).
	 *
	 * @param  string $hash The id hash of the photo
	 * @param  string $type The type of the file we want.
	 *
	 * @return \Illuminate\Http\Response
	 * @throws BadRequestException
	 * @throws NotFoundException
	 */
	public function show($hash, $type)
	{
		$ids = Hashids::decode($hash, $type);
		if (count($ids) === 0) {
			throw new NotFoundException("Nothing found for {$hash}.");
		}
		if (count($ids) === 1 && count($this->requestParams->include) === 0) {
			$result = $this->repository->getFile($ids[0], $type, $this->requestParams);
		} else {
			throw new BadRequestException("Multiple photo ids for file retrieval is not supported.");
		}

		if (is_null($result)) {
			throw new NotFoundException("Nothing found for /{$hash}/image/{$type}.");
		} else {
			$response = new ImageResponse($result);
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

	public function options() {
		return parent::options();
	}

}