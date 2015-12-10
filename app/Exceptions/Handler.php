<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Phogra\Exception\PhograException;
use App\Phogra\Response\ExceptionResponse;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        HttpException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
		$file = explode('/',$e->getFile());
        $file = array_slice($file, -2);
		//	TODO: check environment and only give bare bones messages in production
		$content = (object)[
			'message' => "An unexpected error occurred: "
                . $e->getMessage()
                . " in "
                . implode("/", $file)
                . " on line "
                . $e->getLine()
		];
		$status = 500;

		if ($e instanceof PhograException) {
			$content->message = $e->getMessage();
			$status = $e->getCode();
		} elseif( app()->environment('local')) {

            $trace_lines = explode("\n", $e->getTraceAsString());
            $content->stacktrace = array_filter($trace_lines, function($item){

                //  Don't usually care about all the stacktrace lines dealing with library calls.
                //  Just want to see the lines related to my code
                $iCare = strpos($item, "\\app\\");
                return $iCare !== false ? true : false;
            });
        }

        $response = new ExceptionResponse($content);
        $response->http_code = $status;

        return $response->send();
    }
}
