<?php

namespace App\Exceptions;

use Exception;

class QuotaExceededException extends Exception
{
    protected $message = 'You have no message exchanges remaining on your current plan.';
}
