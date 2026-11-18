<?php
    
namespace App\Http\Controllers\Frontend\Patient;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class VoiceTranscriptionController extends Controller
{
    public function transcribe(Request $request)
    {
        $request->validate([
            'audio' => [
                'required',
                'file',
                'max:25600',
                // Microphone recordings are audio/webm. PHP labels that "weba",
                // so an extension list of "webm" rejects a valid recording.
                'mimetypes:audio/webm,video/webm,audio/ogg,video/ogg,audio/mpeg,audio/mp4,video/mp4,audio/wav,audio/x-wav,audio/x-m4a,audio/flac,application/octet-stream',
            ],
        ]);

        try {
            $apiKey = config('services.openai.key');
            if (! is_string($apiKey) || $apiKey === '') {
                return response()->json([
                    'success' => false,
                    'message' => 'Transcription is not configured. Add OPENAI_API_KEY on the server and run php artisan config:clear.',
                ], 503);
            }

            $audio = $request->file('audio');
            $mime = $audio->getMimeType() ?: $audio->getClientMimeType();
            $extension = match ($mime) {
                'audio/wav', 'audio/x-wav' => 'wav',
                'audio/webm', 'video/webm' => 'webm',
                'audio/ogg', 'video/ogg' => 'ogg',
                'audio/mpeg' => 'mp3',
                'audio/mp4', 'video/mp4', 'audio/x-m4a' => 'm4a',
                'audio/flac' => 'flac',
                default => 'wav',
            };

            $response = Http::withToken($apiKey)
            ->timeout(120)
            ->attach(
                'file',
                file_get_contents($audio->getRealPath()),
                'recording.'.$extension
            )
            ->post(
                'https://api.openai.com/v1/audio/transcriptions',
                [
                    'model' => config(
                        'services.openai.transcription_model'
                    ),
                ]
            );

            if ($response->failed()) {
                Log::error('Voice transcription API failed', [
                    'status' => $response->status(),
                    'mime' => $mime,
                    'body' => $response->json('error.message') ?: $response->body(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $response->json('error.message') ?: 'Unable to transcribe audio.',
                ], 502);
            }

            return response()->json([
                'success' => true,
                'text' => $response->json('text'),
            ]);

        } catch (\Throwable $e) {

            Log::error('Voice transcription error', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
            ], 500);
        }
    }
}

