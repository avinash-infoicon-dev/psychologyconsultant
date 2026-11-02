<?php

namespace App\Services\Speech;

use App\Contracts\SpeechDriverInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class WhisperService implements SpeechDriverInterface
{
    // public function transcribe(string $audioBytes, string $mimeType, ?string $language = null): array
    // {
    //     $apiKey = config('speech.whisper.api_key');

    //     if (! $apiKey) {
    //         throw new RuntimeException('OPENAI_API_KEY is not configured — speech.whisper.api_key is empty.');
    //     }

    //     $extension = $this->extensionForMime($mimeType);
    //     $language = $language && $language !== 'auto' ? $language : null;

    //     $request = Http::withToken($apiKey)
    //         ->timeout(config('speech.whisper.timeout', 60))
    //         ->attach('file', $audioBytes, "audio.{$extension}")
    //         ->asMultipart();

    //     $response = $request->post(config('speech.whisper.base_url'), array_filter([
    //         'model' => config('speech.whisper.model', 'whisper-1'),
    //         'response_format' => config('speech.whisper.response_format', 'verbose_json'),
    //         'language' => $language,
    //     ]));

    //     if (! $response->successful()) {
    //         Log::error('Whisper transcription failed', ['status' => $response->status(), 'body' => $response->body()]);

    //         throw new RuntimeException('Speech-to-text request failed: '.$response->status());
    //     }

    //     $json = $response->json();

    //     return [
    //         'text' => $json['text'] ?? '',
    //         'language' => $json['language'] ?? null,
    //     ];
    // }

    // protected function extensionForMime(string $mimeType): string
    // {
    //     return match ($mimeType) {
    //         'audio/mpeg' => 'mp3',
    //         'audio/mp4' => 'm4a',
    //         'audio/wav' => 'wav',
    //         'audio/webm' => 'webm',
    //         'audio/ogg' => 'ogg',
    //         default => 'mp3',
    //     };
    // }


    public function transcribe(string $audioBytes, string $mimeType, ?string $language = null): array
    {
        $extension = $this->extensionForMime($mimeType);
        $language = $language ?: config('speech.default_language');
        $language = $language && $language !== 'auto' ? $language : null;

        $response = Http::timeout(
                config('speech.whisper.timeout', 60)
            )
            ->attach(
                'file',                   // field name expected by Python
                $audioBytes,
                "audio.$extension"
            )
            ->post(config('speech.whisper.base_url'), array_filter([
                'language' => $language,
                'model' => config('speech.whisper.model', 'base'),
            ]));

        if (! $response->successful()) {
            Log::error('Python transcription failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new RuntimeException(
                'Speech-to-text request failed: '.$response->status()
            );
        }

        // print_r($response->json());
        // //print_r($response->body());
        // exit;

        $json = $response->json();

        $text = trim((string) ($json['text'] ?? ''));

        if ($text === '') {
            $text = collect($json['segments'] ?? [])
                ->pluck('text')
                ->map(fn ($segment) => trim((string) $segment))
                ->filter()
                ->implode(' ');
        }

        return [
            'text' => $text,
            'language' => $json['language'] ?? null,
        ];
    }

    protected function extensionForMime(string $mimeType): string
    {
        return match ($mimeType) {
            'audio/mpeg' => 'mp3',
            'audio/mp4' => 'm4a',
            'audio/x-m4a' => 'm4a',
            'audio/wav', 'audio/x-wav' => 'wav',
            'audio/webm' => 'webm',
            'audio/ogg' => 'ogg',
            default => 'wav',
        };
    }



}
