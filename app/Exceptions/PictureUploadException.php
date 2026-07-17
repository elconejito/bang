<?php

namespace App\Exceptions;

use RuntimeException;
use Throwable;

class PictureUploadException extends RuntimeException
{
    public function __construct(string $message, Throwable $previous)
    {
        parent::__construct($message, 0, $previous);
    }
}
