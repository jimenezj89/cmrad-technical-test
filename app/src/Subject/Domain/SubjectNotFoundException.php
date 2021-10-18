<?php

namespace App\Subject\Domain;

use Exception;

final class SubjectNotFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct('Subject does not exists.');
    }
}