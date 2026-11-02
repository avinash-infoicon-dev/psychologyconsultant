<?php

namespace App\Exceptions;

use Exception;

class ChannelNotAllowedException extends Exception
{
    public function __construct(?string $message = null)
    {
        parent::__construct($message ?? __('message_api.channel_not_allowed'));
    }
}
