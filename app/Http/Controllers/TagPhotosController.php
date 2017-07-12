<?php
/**
 * User: Sean
 * Date: 7/9/2017
 */

namespace App\Http\Controllers;

use App\Phogra\Exception\BadRequestException;
use App\Phogra\Exception\NotFoundException;
use App\Phogra\Photo;
use App\Phogra\Response\Photos as PhotosResponse;
use Illuminate\Http\Request;

class TagPhotosController extends BaseController
{
    private $repository;

    public function __construct(Request $request, Photo $repository)
    {

        parent::__construct($request);

        $this->repository = $repository;
        $this->middleware('phogra.anonymous.token', ['only' => ['index', 'show']]);
        $this->middleware('phogra.jwt.auth', ['except' => ['index', 'show', 'options']]);
    }

    /**
     * Return all photos for a given tag
     *
     * @param $tag_name  string the tag name
     *
     * @return \Illuminate\Http\Response
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function index($tag_name)
    {
        $result = $this->repository->findByTag($tag_name, $this->requestParams);

        if (is_null($result)) {
            throw new NotFoundException("No data found for /tags/{$tag_name}/photos.");
        }

        $response = new PhotosResponse($result);
        return $response->send();
    }


    public function store()
    {
        
    }


    public function update()
    {

    }


    public function show ()
    {

    }


    public function destroy()
    {
        
    }

}