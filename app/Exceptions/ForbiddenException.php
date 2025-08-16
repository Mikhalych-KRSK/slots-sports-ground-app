<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;

class ForbiddenException extends HttpResponseException
{
    public function __construct(string $message = 'Access denied')
    {
        parent::__construct(response()->json([
            'message' => $message,
        ], 403));
    }
}
