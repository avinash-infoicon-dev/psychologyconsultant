<?php

namespace App\Exceptions;

use Exception;

class PsychologistUnavailableException extends Exception
{
    public function __construct()
    {
        parent::__construct(__('frontend.chat.psychologist_unavailable'));
    }
}
