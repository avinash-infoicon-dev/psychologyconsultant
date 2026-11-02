<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Speech Driver
    |--------------------------------------------------------------------------
    | Supported: "whisper" (OpenAI Whisper API), "native" (local/self-hosted)
    */
    'driver' => env('SPEECH_DRIVER', 'whisper'),

    'whisper' => [
        'api_key' => env('OPENAI_API_KEY'),
        // 'base_url' => env('WHISPER_BASE_URL', 'https://api.openai.com/v1/audio/transcriptions'),
        // 'model' => env('WHISPER_MODEL', 'whisper-1'),
        // 'timeout' => env('WHISPER_TIMEOUT', 60), // seconds
        // 'response_format' => 'verbose_json', // gives us language + segments

        "base_url" => "http://127.0.0.1:8090/transcribe",
        "model" => "base",
        "timeout" => 60, // seconds
        "api_key" => "",
        "response_format" => "verbose_json", // gives us language + segments
    ],

    'native' => [
        'binary_path' => env('NATIVE_SPEECH_BINARY', '/usr/local/bin/whisper.cpp'),
        'model_path' => env('NATIVE_SPEECH_MODEL', '/models/ggml-base.bin'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Upload Constraints
    |--------------------------------------------------------------------------
    | Chat voice messages prefer admin Messaging settings (messaging.audio.*).
    | These values remain the fallback for dictation / Whisper uploads and when
    | no messaging setting is stored.
    */
    'max_upload_size_kb' => env('SPEECH_MAX_UPLOAD_KB', 25600), // 25MB
    'allowed_mime_types' => ['audio/mpeg', 'audio/mp4', 'audio/wav', 'audio/webm', 'audio/ogg'],

    /*
    |--------------------------------------------------------------------------
    | Language Detection
    |--------------------------------------------------------------------------
    */
    'default_language' => env('SPEECH_DEFAULT_LANGUAGE', 'auto'),
    'supported_languages' => ['en', 'de', 'fr', 'it', 'auto'],

    /*
    |--------------------------------------------------------------------------
    | Storage
    |--------------------------------------------------------------------------
    */
    'storage_disk' => 'private',
    'audio_path' => 'audio',
    'transcript_path' => 'transcripts',

    /*
    |--------------------------------------------------------------------------
    | Queue
    |--------------------------------------------------------------------------
    */
    'queue' => env('SPEECH_QUEUE', 'speech'),
];
