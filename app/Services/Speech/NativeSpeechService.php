<?php

namespace App\Services\Speech;

use App\Contracts\SpeechDriverInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Shells out to a local whisper.cpp (or similar) binary instead of calling
 * an external API — useful for on-prem/data-residency requirements where
 * audio can't leave the server. Requires the binary + model configured in
 * config('speech.native') to actually be present on the host; this class
 * fails loudly rather than silently falling back if they're missing.
 */
class NativeSpeechService implements SpeechDriverInterface
{
    public function transcribe(string $audioBytes, string $mimeType, ?string $language = null): array
    {
        $binary = config('speech.native.binary_path');
        $model = config('speech.native.model_path');

        if (! $binary || ! is_executable($binary)) {
            throw new RuntimeException("Native speech binary not found or not executable: {$binary}");
        }

        $tmpInput = storage_path('app/tmp/'.Str::uuid().'.audio');
        $tmpOutput = $tmpInput.'.txt';

        @mkdir(dirname($tmpInput), 0755, true);
        file_put_contents($tmpInput, $audioBytes);

        try {
            $command = array_merge(
                [$binary, '-m', $model, '-f', $tmpInput, '-otxt', '-of', $tmpInput],
                $language ? ['-l', $language] : []
            );

            $result = Process::timeout(300)->run($command);

            if (! $result->successful()) {
                Log::error('Native speech transcription failed', ['error' => $result->errorOutput()]);
                throw new RuntimeException('Native transcription process failed.');
            }

            $text = is_file($tmpOutput) ? trim(file_get_contents($tmpOutput)) : '';

            return ['text' => $text, 'language' => $language];
        } finally {
            @unlink($tmpInput);
            @unlink($tmpOutput);
        }
    }
}
