<?php

namespace App\Http\Controllers;

use App\Phogra\Exception\BadRequestException;
use App\Phogra\Exception\NotFoundException;
use App\Phogra\Photo;
use App\Phogra\Response\Photos as PhotosResponse;
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
     * @param $gallery_id  int  the gallery id to filter on
     *
     * @return \App\Phogra\Response
     */
	public function index($gallery_id)
	{
		$photos = $this->repository->byGalleryId($gallery_id, $this->requestParams);

		$response = new PhotosResponse($photos);
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
     * @param $gallery_id  integer  the gallery_id
     * @param $photo_ids  integer|string  a single integer that is a photo_id or string of comma separated ids
	 *
	 * @return \Illuminate\Http\Response
	 * @throws BadRequestException
	 * @throws NotFoundException
	 */
	public function show($gallery_id, $photo_ids)
    {
        if (!is_numeric($gallery_id)) {
            if (strpos($gallery_id, ',') !== false) {
                throw new BadRequestException('Multiple gallery ids not supported.');
            }
            throw new BadRequestException('Non-numeric gallery_id given.');
        }

        if (!is_numeric($photo_ids)) //	Pull out all the commas. It should still be numeric.
        {
            $quickcheck = str_replace(',', '', $photo_ids);
            if (!is_numeric($quickcheck)) {
                throw new BadRequestException('Non-numeric ids given. Spaces in your list? Or are you being naughty?');
            }
        }

        $result = $this->repository->byGalleryAndPhotoIds($gallery_id, $photo_ids, $this->requestParams);

		if (is_null($result)) {
			throw new NotFoundException("No data found for /galleries/{$gallery_id}/photos/{$photo_ids}.");
		} else {
			$response = new GalleriesResponse($result);
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
