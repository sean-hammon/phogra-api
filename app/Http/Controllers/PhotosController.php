<?php

namespace App\Http\Controllers;

use App\Phogra\Exception\BadRequestException;
use App\Phogra\Exception\NotFoundException;
use App\Phogra\Exception\InvalidOperationException;
use App\Phogra\Exception\InvalidJsonException;
use App\Phogra\File\Processor;
use App\Phogra\Photo;
use App\Phogra\Eloquent\Photo as PhotoModel;
use App\Phogra\Response\Photos as PhotosResponse;
use Hashids;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PhotosController extends BaseController
{

    private $repository;

    public function __construct(Request $request, Photo $repository)
    {
        parent::__construct($request);
        $this->repository = $repository;
        $this->middleware('phogra.jwt.auth', ['except' => ['index', 'show']]);
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
     * @return \Illuminate\Http\Response
     *
     * @throws BadRequestException
     * @throws InvalidJsonException
     * @throws \App\Phogra\Exception\InvalidParameterException
     */
    public function store()
    {
        $json = $this->request->getContent();
        $files = [];

        //  If $json is empty at this point, it's probably a multi-part post.
        //  This only works with POST. PHP doesn't do any content parsing otherwise.
        if (empty($json)) {
            $json = json_decode($this->request->input('json'), true);
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
            $processed_files = array_keys($files);
            $request_params = array_keys($this->request->all());

            //  Take the json parameter out of the mix.
            //  TODO: What about parameters passed in the query string?? Will there ever be any here?
            $json_key = array_search('json', $request_params);
            if ($json_key !== FALSE) {
                unset($request_params[$json_key]);
            }
            $missed_params = array_diff($request_params, $processed_files);
            if (!empty($missed_params)) {
                $warnings = app('Warnings');
                $warnings->addWarning('Incoming parameter(s) not recognized. Typo? : ' . implode(',', $missed_params));
            }

        } else {
            $json = json_decode($json, true);
        }

        if (json_last_error()) {
            throw new InvalidJsonException("Invalid JSON: " . json_last_error_msg());
        }

        $photo = $this->repository->create($json);
        foreach ($files as $type => $file) {
            $path = $this->movePostFile($file);

            $processor = new Processor($photo->id, $path);
            $processor->make($type);
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
     * @param  int $hash
     *
     * @return \Illuminate\Http\Response
     * @throws \App\Phogra\Exception\BadRequestException
     * @throws \App\Phogra\Exception\NotFoundException
     */
    public function update($hash)
    {

        $ids = Hashids::decode($hash);
        if (count($ids) === 0) {
            throw new NotFoundException("Invalid hash.");
        }
        if (count($ids) > 1) {
            throw new BadRequestException("Multiple IDs are not currently supported.");
        }
        $photoID = $ids[0];

        $requestData = $this->getPutData();

        $photo = PhotoModel::find($photoID);
        $fileData = null;
        if (isset($requestData["json"])) {
            $photoData = json_decode($requestData["json"], true);
            if (!empty($photoData)) {
                $photo->fill($photoData);
                $photo->update();
            }
        }

        $fileTypes = get_object_vars(config('phogra.fileTypes'));
        foreach ($fileTypes as $type => $info) {

            if (isset($requestData[$type])) {

                $processor = new Processor($photo->id, $requestData[$type]);
                $processor->makeOrReplace($type);

            }

        }

        $response = new PhotosResponse($photo);
        return $response->send();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
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
        $randomized = '/tmp_' . bin2hex(openssl_random_pseudo_bytes(16));
        $file->move(config("phogra.photoTempDir"), $randomized);
        return config("phogra.photoTempDir") . DIRECTORY_SEPARATOR . $randomized;
    }

}
