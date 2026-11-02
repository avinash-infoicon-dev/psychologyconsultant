<?php

namespace App\Events;

use App\Models\AudioMessage;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AudioUploaded
{
    use Dispatchable, SerializesModels;

    public function __construct(public AudioMessage $audioMessage) {}
}
