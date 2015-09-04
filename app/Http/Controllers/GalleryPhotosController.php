<?php

namespace App\Http\Controllers;

use App\Phogra\Exception\BadRequestException;
use App\Phogra\Exception\NotFoundException;
use App\Phogra\Photo;
use App\Phogra\Response\Photos as PhotosResponse;
use Hashids;
use Illuminate\Http\Request;

class GalleryPhotosController extends BaseController {

	private $repository;

	public function __construct(Request $request, Photo $repository) {

		parent::__construct($request);

		$this->repository = $repository;
	}

    /**
     * Return all gallery records
     *
     * @param $gallery_hash  string  the gallery hash
     *
     * @return \App\Phogra\Response
     * @throws BadRequestException
     * @throws NotFoundException
     */
	public function index($gallery_hash)
	{
        $gallery_ids = Hashids::decode($gallery_hash);
        if (count($gallery_ids) === 0) {
            throw new NotFoundException("No data found for {$gallery_hash}.");
        }

        if (count($gallery_ids) > 1) {
            throw new BadRequestException('Multiple gallery ids not supported.');
        }

		$result = $this->repository->byGalleryId($gallery_ids[0], $this->requestParams);

        if (is_null($result)) {
            throw new NotFoundException("No data found for /galleries/{$gallery_hash}/photos.");
        }

        $response = new PhotosResponse($result);
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
     * @param $gallery_hash  string  the gallery_id hash
     * @param $photo_hash    string  hash for the photos
	 *
	 * @return \Illuminate\Http\Response
	 * @throws BadRequestException
	 * @throws NotFoundException
	 */
	public function show($gallery_hash, $photo_hash)
    {
		$gallery_ids = Hashids::decode($gallery_hash);
		if (count($gallery_ids) === 0) {
			throw new NotFoundException("No data found for /galleries/{$gallery_hash}/photos/{$photo_hash}.");
		}

        if (count($gallery_ids) > 1) {
			throw new BadRequestException('Multiple gallery ids not supported.');
        }

		$photo_ids = Hashids::decode($photo_hash);
        if (count($photo_ids) === 0)
        {
			throw new NotFoundException("No data found for /galleries/{$gallery_hash}/photos/{$photo_hash}.");
        }

        $result = $this->repository->byGalleryAndPhotoIds($gallery_ids[0], $photo_ids, $this->requestParams);

		if (is_null($result)) {
			throw new NotFoundException("No data found for /galleries/{$gallery_hash}/photos/{$photo_hash}.");
		}

        $response = new GalleriesResponse($result);
        return $response->send();
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
