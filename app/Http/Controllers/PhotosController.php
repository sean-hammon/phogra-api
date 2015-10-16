<?php

namespace App\Http\Controllers;

use App\Phogra\Exception\BadRequestException;
use App\Phogra\Exception\NotFoundException;
use App\Phogra\Exception\InvalidOperationException;
use App\Phogra\Exception\InvalidJsonException;
use App\Phogra\File\Processor;
use App\Phogra\Photo;
use App\Phogra\Response\Photos as PhotosResponse;
use Hashids;
use Illuminate\Http\Request;

class PhotosController extends BaseController
{

    private $repository;

    public function __construct(Request $request, Photo $repository)
    {
        parent::__construct($request);
        $this->repository = $repository;
        $this->middleware('jwt.auth', ['except' => ['index', 'show']]);
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
     * @return Response
     *
     * @throws BadRequestException
     * @throws InvalidJsonException
     * @throws \App\Phogra\Exception\InvalidParameterException
     */
    public function store()
    {
        $json = $this->request->getContent();
        $file = null;

        //  If $json is empty at this point, it's probably a multi-part post.
        if (empty($json)) {
            $json = json_decode($this->request->input('json'), true);
            $file = $this->request->file('photo');
            if (!empty($file) && $file->isValid() === false) {
                throw new BadRequestException($file->getErrorMessage());
            }

        } else {
            $json = json_decode($json);
        }

        if (json_last_error()) {
            throw new InvalidJsonException(json_last_error_msg());
        }
        $photo = $this->repository->create($json);
        if (isset($file)) {
            $file->move(config("phogra.photoTempDir"), $file->getClientOriginalName());
            $path = config("phogra.photoTempDir") . DIRECTORY_SEPARATOR . $file->getClientOriginalName();

            $processor = new Processor($photo->id, $path);
            $processor->make('original');

            $typeConfig = config('phogra.fileTypes');
            foreach ($typeConfig->original->autoGenerate as $type) {
                $processor->make($type);
            }
        }

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
        if (count($ids) === 1 && count($this->requestParams->include) === 0) {
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

    public function options()
    {
        return parent::options();
    }

}
