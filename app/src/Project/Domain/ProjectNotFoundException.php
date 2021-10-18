<?php

namespace App\Project\Domain;

use Exception;

final class ProjectNotFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct('Project does not exists.');
    }
}