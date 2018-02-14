<?php

namespace Reinforcement\Exceptions;

use RuntimeException;

class BadRequestException extends RuntimeException
{
    protected $data = [];

    public function __construct($message = "Bad Request", $data = null)
    {
        $this->data = (array) $data;
        parent::__construct($message);
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        $response = [
            "message" => $this->message,
            "details" => $this->data,
        ];
        return response($response, 400);
    }

    public function getData()
    {
        return (array) $this->data;
    }
}