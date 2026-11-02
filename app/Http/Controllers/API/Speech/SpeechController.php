<?php

namespace App\Http\Controllers\API\Speech;

use App\Contracts\SpeechDriverInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\UploadAudioRequest;
use Illuminate\Http\JsonResponse;
use RuntimeException;

/**
 * Standalone transcription — e.g. a patient dictating a note before
 * sending it as text, rather than sending the audio itself as a
 * message. Message-attached audio is transcribed automatically via
 * ProcessSpeechToText; this endpoint is for one-off, synchronous use.
 */
class SpeechController extends Controller
{
    public function __construct(protected SpeechDriverInterface $driver) {}

    public function transcribe(UploadAudioRequest $request): JsonResponse
    {
        $file = $request->file('audio');

        try {
            $result = $this->driver->transcribe(
                file_get_contents($file->getRealPath()),
                //$file->getMimeType(),
                $request->audioMimeType(),
            );
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 502);
        }

        return response()->json(['data' => $result]);
    }
}
