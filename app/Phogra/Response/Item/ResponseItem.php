<?php

namespace app\Phogra\Response\Item;

class ResponseItem
{
    public $type;
    public $id;
    public $links;
    public $attributes;
    public $relationships;

    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
    }
}