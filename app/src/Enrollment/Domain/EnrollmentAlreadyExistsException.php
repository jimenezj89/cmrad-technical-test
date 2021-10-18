<?php

namespace App\Enrollment\Domain;

use Exception;

final class EnrollmentAlreadyExistsException extends Exception
{
    public function __construct()
    {
        parent::__construct('Subject is already enrolled');
    }
}