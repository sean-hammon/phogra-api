<?php

namespace App\Phogra\Response;

class Debug
{
    private $debug;

    public function __construct()
    {
        $this->debug = [];
    }

    public function addMessage($message)
    {
        $this->debug[] = $message;
    }

    public function getMessages()
    {
        return $this->debug;
    }

    public function count()
    {
        return count($this->debug);
    }

}