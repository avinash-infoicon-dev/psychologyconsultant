<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Per-message limits (§5.5) — by role
    |--------------------------------------------------------------------------
    | Runtime values prefer DB settings written by the admin Messaging page:
    |   messaging.patient.max_text_chars
    |   messaging.patient.max_audio_seconds
    |   messaging.psychologist.max_text_chars
    |   messaging.psychologist.max_audio_seconds
    |
    | Use 0 for unlimited. Legacy flat keys (messaging.max_text_chars /
    | messaging.max_audio_seconds) remain readable as a patient fallback.
    */
    'patient' => [
        'max_text_chars' => (int) (env('MESSAGING_PATIENT_MAX_TEXT_CHARS') ?? env('MESSAGING_MAX_TEXT_CHARS', 2000)),
        'max_audio_seconds' => (int) (env('MESSAGING_PATIENT_MAX_AUDIO_SECONDS') ?? env('MESSAGING_MAX_AUDIO_SECONDS', 60)),
    ],

    'psychologist' => [
        'max_text_chars' => (int) env('MESSAGING_PSYCHOLOGIST_MAX_TEXT_CHARS', 0),
        'max_audio_seconds' => (int) env('MESSAGING_PSYCHOLOGIST_MAX_AUDIO_SECONDS', 0),
    ],

    // Legacy flat defaults (kept for older .env / fallbacks).
    'max_text_chars' => (int) env('MESSAGING_MAX_TEXT_CHARS', 2000),
    'max_audio_seconds' => (int) env('MESSAGING_MAX_AUDIO_SECONDS', 60),

    /*
    |--------------------------------------------------------------------------
    | Voice message files (admin Messaging page)
    |--------------------------------------------------------------------------
    | Runtime prefers DB settings:
    |   messaging.audio.max_upload_size_kb
    |   messaging.audio.allowed_formats  (keys: webm, ogg, mp3, wav, m4a)
    |
    | Legacy fallback: config/speech.php (dictation / Whisper still uses that
    | file when no messaging setting is stored).
    */
    'audio' => [
        'max_upload_size_kb' => (int) env(
            'MESSAGING_AUDIO_MAX_UPLOAD_KB',
            env('SPEECH_MAX_UPLOAD_KB', 25600)
        ),
        'allowed_formats' => ['webm', 'ogg', 'mp3', 'wav', 'm4a'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Messages per exchange
    |--------------------------------------------------------------------------
    | Plan column NULL inherits global, then this default. 0 = unlimited.
    | Runtime reads Setting keys:
    |   messaging.patient.messages_per_exchange
    |   messaging.psychologist.messages_per_exchange
    */
    'messages_per_exchange' => [
        'patient' => (int) env('MESSAGING_PATIENT_MESSAGES_PER_EXCHANGE', 5),
        'psychologist' => (int) env('MESSAGING_PSYCHOLOGIST_MESSAGES_PER_EXCHANGE', 5),
    ],

];
