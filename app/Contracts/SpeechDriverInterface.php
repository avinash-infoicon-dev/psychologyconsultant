<?php

namespace App\Contracts;

interface SpeechDriverInterface
{
    /**
     * Transcribe raw audio bytes and return:
     * ['text' => string, 'language' => ?string]
     */
    public function transcribe(string $audioBytes, string $mimeType, ?string $language = null): array;
}
