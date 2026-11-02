<?php

namespace App\Repositories;

use App\Models\AudioMessage;

class SpeechRepository
{
    public function markProcessing(AudioMessage $audioMessage): void
    {
        $audioMessage->update(['transcription_status' => 'processing']);
    }

    public function saveTranscript(AudioMessage $audioMessage, string $text, ?string $language): void
    {
        $audioMessage->update([
            'transcript' => $text,
            'language' => $language,
            'transcription_status' => 'completed',
        ]);
    }

    public function markFailed(AudioMessage $audioMessage): void
    {
        $audioMessage->update(['transcription_status' => 'failed']);
    }
}
