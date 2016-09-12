<?php

namespace App\Http\Controllers;

use App\Phogra\Exception\BadRequestException;
use App\Phogra\Exception\NotFoundException;
use App\Phogra\Gallery;
use App\Phogra\Response\Galleries as GalleriesResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Hashids;

class GalleriesController extends BaseController
{

    private $repository;

    public function __construct(Request $request, Gallery $repository)
    {

        $this->allowedParams[] = 'empty';
        parent::__construct($request);

        $this->repository = $repository;
        $this->middleware('phogra.anonymous.token', ['only' => ['index', 'show']]);
	    $this->middleware('phogra.jwt.auth', ['except' => ['index', 'show', 'options']]);
    }

    /**
     * Return all gallery records
     *
     * @return Response
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
     * @throws BadRequestException
     */
    public function store()
    {
        $data = $this->getRequestBody();
        $gallery = $this->repository->create($data);

        $response = new GalleriesResponse($gallery);
        return $response->send();
    }

    /**
     * Display the specified galleries.
     *
     * @param  string $hash integer row id for a single gallery OR
     *                          string of comma separated ids
     *
     * @return Response
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function show($hash)
    {
        $ids = Hashids::decode($hash);
        if (count($ids) === 0) {
            throw new NotFoundException("No data found for {$hash}");
        }

        if (count($ids) == 1) {
		$result = $this->repository->one($ids[0], $this->requestParams);
        } else {
            $result = $this->repository->multiple($ids, $this->requestParams);
        }

        if (is_null($result)) {
            throw new NotFoundException("No data found for {$hash}.");
        } else {
            $response = new GalleriesResponse($result);
            return $response->send();
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

}
