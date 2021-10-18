<?php

namespace App\Subject\Domain;

use Exception;

final class SubjectAlreadyExistsException extends Exception
{
    public function __construct()
    {
        parent::__construct('Subject already exists.');
    }
}