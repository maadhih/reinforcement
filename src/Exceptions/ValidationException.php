<?php

namespace Reinforcement\Exceptions;

use RuntimeException;
use Illuminate\Support\MessageBag;

class ValidationException extends RuntimeException
{
    /**
     * The underlying response instance.
     *
     * @var \Symfony\Component\HttpFoundation\Response
     */
    protected $errors;

    /**
     * Create a new HTTP response exception instance.
     *
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return void
     */
    public function __construct(MessageBag $errors)
    {
        parent::__construct('The given data failed to pass validation.');
        $this->errors = $errors;
    }

    /**
     * Get the underlying response instance.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getErrors()
    {
        return $this->errors;
    }

    public function render($request) {
         return response()->json(
            ['errors' => $this->getErrors()->getMessages()], 422);
    }
}
