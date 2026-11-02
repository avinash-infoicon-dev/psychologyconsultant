<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Browser MediaRecorder output uses the WebM/Ogg container, which PHP's finfo
 * reports as video/webm or video/ogg even when the stream is audio-only.
 * Those containers are accepted here in addition to the configured audio types.
 */
class UploadAudioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $maxKb = config('speech.max_upload_size_kb', 25600);

        // $mimes = collect(config('speech.allowed_mime_types', []))
        //     ->map(fn ($mime) => str_replace('audio/', '', $mime))
        //     ->implode(',');

        $mimeTypes = array_unique(array_merge(
            config('speech.allowed_mime_types', []),
            ['video/webm', 'video/ogg', 'audio/x-wav', 'audio/x-m4a', 'application/octet-stream'],
        ));

        return [
            // /'audio' => ['required', 'file', 'max:'.$maxKb, 'mimetypes:'.implode(',', config('speech.allowed_mime_types', []))],
            'audio' => ['required', 'file', 'max:'.$maxKb, 'mimetypes:'.implode(',', $mimeTypes)],
        ];
    }

    /** Container mime normalised to the audio equivalent the speech driver expects. */
    public function audioMimeType(): string
    {
        $mime = $this->file('audio')?->getMimeType();

        return match ($mime) {
            'video/webm' => 'audio/webm',
            'video/ogg' => 'audio/ogg',
            'audio/x-wav' => 'audio/wav',
            'audio/x-m4a' => 'audio/mp4',
            'application/octet-stream', null => $this->mimeFromExtension(),
            default => $mime,
        };
    }

    protected function mimeFromExtension(): string
    {
        return match (strtolower((string) $this->file('audio')?->getClientOriginalExtension())) {
            'mp3' => 'audio/mpeg',
            'm4a', 'mp4' => 'audio/mp4',
            'wav' => 'audio/wav',
            'ogg', 'oga' => 'audio/ogg',
            default => 'audio/webm',
        };
    }
}
