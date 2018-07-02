<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class ApiException extends Exception
{
    protected $statusCode;
    protected $message;
    protected $code;
    protected $error;

    public function __construct(
        string $message,
        int $statusCode = 422,
        int $code = 0,
        string $error = 'api_error',
        Throwable $previous = null)
    {
        $this->code = $code;
        $this->message = $message;
        $this->statusCode = $statusCode;
        $this->error = $error;
        parent::__construct($message, $code, $previous);
    }

    public function render() {
        return response([
            'error' => $this->error,
            'message' => $this->message,
            'code' => $this->code,
        ], $this->statusCode);
    }
}
