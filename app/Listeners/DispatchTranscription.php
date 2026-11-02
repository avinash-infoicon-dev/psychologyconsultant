<?php

namespace App\Listeners;

use App\Events\AudioUploaded;
use App\Jobs\ProcessSpeechToText;

class DispatchTranscription
{
    public function handle(AudioUploaded $event): void
    {
        ProcessSpeechToText::dispatch($event->audioMessage)->onQueue(config('speech.queue', 'speech'));
    }
}
