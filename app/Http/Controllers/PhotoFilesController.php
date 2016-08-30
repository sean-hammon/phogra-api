<?php

namespace App\Http\Controllers;

use Hashids;
use Illuminate\Http\Request;
use App\Phogra\Photo;
use App\Phogra\Eloquent\Photo as PhotoModel;
use App\Phogra\Response\Files as FilesResponse;
use App\Phogra\Response\Photos as PhotosResponse;
use App\Phogra\Exception\BadRequestException;
use App\Phogra\Exception\NotFoundException;
use App\Phogra\File\Processor;

class PhotoFilesController extends ApiController
{
    private $repository;

    public function __construct(Request $request, Photo $repository)
    {
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
     * @param $hashid
     *
     * @return \App\Http\Controllers\Response
     * @throws \App\Phogra\Exception\BadRequestException
     * @throws \App\Phogra\Exception\NotFoundException
     */
    public function store($hashid)
    {
        $photo_ids = Hashids::decode($hashid);
        if (count($photo_ids) === 0) {
            throw new NotFoundException("Invalid hash.");
        }
        if (count($photo_ids) > 1) {
            throw new BadRequestException("Multiple IDs are not currently supported.");
        }
        $photoID = $photo_ids[0];

        $json = $this->request->getContent();
        $files = [];

        //  If $json is not empty at this point, it's probably wasn't a multi-part post.
        //  This only works with POST. PHP doesn't do any content parsing otherwise.
        if (!empty($json)) {
            throw new BadRequestException("You must send a file to this endpoint or it has no work to do.");
        }

        if ($this->request->has('json')) {
            //TODO: Add a warning that json was ignored.
        }

        $fileTypes = get_object_vars(config('phogra.fileTypes'));
        foreach ($fileTypes as $type => $info) {
            if ($this->request->hasFile($type)){
                $file = $this->request->file($type);
                if ($file->isValid() === false) {
                    throw new BadRequestException($file->getErrorMessage());
                }
                $files[$type] = $file;
            }
        }

        foreach ($files as $type => $file) {
            $path = $this->movePostFile($file);

            $processor = new Processor($photoID, $path);
            $processor->make($type);
        }

        $photo = PhotoModel::find($photoID);
        $response = new PhotosResponse($photo);
        return $response->send();
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
            throw new NotFoundException("Nothing found for {$hash}.");
        } else {
            $response = new FilesResponse($result);
            return $response->send();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param string $hashid
     *
     * @return \App\Http\Controllers\Response
     * @throws \App\Phogra\Exception\BadRequestException
     * @throws \App\Phogra\Exception\NotFoundException
     * @internal param string $id
     */
    public function update($hashid, $type)
    {
        $photo_ids = Hashids::decode($hashid);
        if (count($photo_ids) === 0) {
            throw new NotFoundException("Invalid hash.");
        }
        if (count($photo_ids) > 1) {
            throw new BadRequestException("Multiple IDs are not currently supported.");
        }
        $photoID = $photo_ids[0];


        $requestData = $this->getPutData();
        if (isset($requestData['json'])) {
            //TODO: Add a warning that json was ignored.
        }

        if (isset($requestData['file'])) {

            $processor = new Processor($photoID, $requestData['file']);
            $processor->makeOrReplace($type);

        }

        $photo = PhotoModel::find($photoID);
        $response = new PhotosResponse($photo);
        return $response->send();

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

    private function movePostFile($file)
    {
        $randomized = 'tmp_' . bin2hex(openssl_random_pseudo_bytes(16));
        $file->move(config("phogra.photoTempDir"), $randomized);
        return config("phogra.photoTempDir") . DIRECTORY_SEPARATOR . $randomized;
    }

}