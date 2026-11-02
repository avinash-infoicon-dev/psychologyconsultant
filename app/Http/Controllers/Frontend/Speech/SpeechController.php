<?php

namespace App\Http\Controllers\Frontend\Speech;

use App\Contracts\SpeechDriverInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\TranscribeRecordingRequest;
use Illuminate\Http\JsonResponse;
use RuntimeException;

/**
 * Session-auth wrapper around the same speech driver used by the patient API
 * (`POST /api/v1/patient/speech/transcribe`), so the web chat can transcribe
 * mic recordings without a JWT bearer token.
 */
class SpeechController extends Controller
{
    public function __construct(protected SpeechDriverInterface $driver) {}

    public function transcribe(TranscribeRecordingRequest $request): JsonResponse
    {
        $file = $request->file('audio');

        try {
            $result = $this->driver->transcribe(
                file_get_contents($file->getRealPath()),
                $request->audioMimeType(),
            );
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 502);
        }

        return response()->json(['data' => $result]);
    }
}
