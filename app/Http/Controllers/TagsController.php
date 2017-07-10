<?php
/**
 * User: Dez
 * Date: 10/8/2016
 */

namespace App\Http\Controllers;

use App\Phogra\Tag;
use App\Phogra\Eloquent\Tag as TagModel;
use App\Phogra\Exception\InvalidParameterException;
use App\Phogra\Exception\NotFoundException;
use App\Phogra\Response\Tags as TagResponse;
use Illuminate\Http\Request;

class TagsController extends BaseController
{
	private $repository;

	public function __construct(Request $request, Tag $repository)
	{
		parent::__construct($request);

		$this->repository = $repository;
		$this->middleware('phogra.anonymous.token', ['only' => ['index','show','getPhotosByTag']]);
		$this->middleware('phogra.jwt.auth', ['except' => ['index','show','getPhotosByTag','options']]);
	}


	public function index()
	{
		$tags = $this->repository->all($this->requestParams);

		$response = new TagResponse($tags);

		return $response->send();
	}


	public function show($tag)
	{
		$result = $this->repository->one($tag, $this->requestParams);

		if (is_null($result)) {
			throw new NotFoundException("No data found for {$tag}.");
		} else {
			$response = new TagResponse($result);
			return $response->send();
		}
	}


	public function tagPhotos()
	{
		$data = json_decode($this->request->getContent());
		$success = $this->photos->tagPhotos($data->photo_ids, $data->tags);

		if ($success) {
			$tags = TagModel::whereIn("name", $data->tags)->get();
			$response = new TagResponse($tags->all());
			return $response->send();
		} else {
			throw new InvalidParameterException("Tagging photos failed.");
		}
	}

}