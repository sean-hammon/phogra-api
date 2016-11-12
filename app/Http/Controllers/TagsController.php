<?php
/**
 * User: Dez
 * Date: 10/8/2016
 */

namespace App\Http\Controllers;

use App\Phogra\Eloquent\Tag as TagModel;
use App\Phogra\Exception\InvalidParameterException;
use App\Phogra\Exception\NotFoundException;
use App\Phogra\Photo;
use App\Phogra\Response\Tags as TagResponse;
use App\Phogra\Response\Photos as PhotosResponse;
use Illuminate\Http\Request;

class TagsController extends BaseController
{

	private $photos;

	public function __construct(Request $request, Photo $repository)
	{
		parent::__construct($request);

		$this->photos = $repository;
		$this->middleware('phogra.anonymous.token', ['only' => ['getPhotosByTag']]);
		$this->middleware('phogra.jwt.auth', ['except' => ['getPhotosByTag','options']]);
	}

	public function getPhotosByTag($tag) {
		$photos = $this->photos->findByTag($tag, $this->requestParams);


		if (is_null($photos)) {
			throw new NotFoundException("Nothing found for '{$tag}''.");
		} else {
			$response = new PhotosResponse($photos);
			return $response->send();
		}
	}

}