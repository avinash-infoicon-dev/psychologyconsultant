<?php

namespace App\Exceptions;

use Exception;

class ExchangeMessageLimitException extends Exception
{
    public function __construct()
    {
        parent::__construct(__('message_api.exchange_message_limit'));
    }
}
