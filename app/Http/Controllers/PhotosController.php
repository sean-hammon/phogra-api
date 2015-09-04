<?php

namespace App\Http\Controllers;

use Hashids;
use Illuminate\Http\Request;
use App\Phogra\Exception\NotFoundException;
use App\Phogra\Exception\InvalidOperationException;
use App\Phogra\Photo;
use App\Phogra\Response\Photos as PhotosResponse;

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
	 * @throws InvalidJsonException
	 */
	public function store()
	{
		$incoming = json_decode($this->request->getContent());
		if (json_last_error()) {
			throw new InvalidJsonException(json_last_error_msg());
		}

		$photo = $this->repository->create(get_object_vars($incoming));
		$response = new PhotosResponse($photo);
		return $response->send();

	}

	/**
	 * Display the specified photo(s).
	 *
	 * @param  string $hash string hash
	 *
	 * @return \Illuminate\Http\Response
	 * @throws \App\Phogra\Exception\BadRequestException
	 * @throws \App\Phogra\Exception\NotFoundException
	 */
	public function show($hash)
	{
		$ids = Hashids::decode($hash);
        if (count($ids) === 0) {
            throw new NotFoundException("Nothing found for {$hash}.");
        }
		if (count($ids) === 1) {
			$result = $this->repository->one($ids[0], $this->requestParams);
		} else {
			$result = $this->repository->multiple($ids, $this->requestParams);
		}

		if (is_null($result)) {
            throw new NotFoundException("Nothing found for {$hash}.");
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

	public function options() {
		return parent::options();
	}


}
