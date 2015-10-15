<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sean
 * Date: 9/7/2015
 * Time: 9:40 PM
 */

namespace App\Phogra\Response;

class ExceptionResponse extends BaseResponse
{
    public function __construct($data)
    {
        unset($this->links);
        $this->data = $data;
    }

}