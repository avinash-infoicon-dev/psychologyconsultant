<?php

namespace App\Http\Requests\Frontend;

//use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\UploadAudioRequest;

/**
 * Browser MediaRecorder output uses the WebM/Ogg container, which PHP's finfo
 * reports as video/webm or video/ogg even when the stream is audio-only.
 * Those containers are accepted here in addition to the configured audio types.
 */
class TranscribeRecordingRequest extends UploadAudioRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    // public function rules(): array
    // {
    //     $maxKb = config('speech.max_upload_size_kb', 25600);

    //     $mimeTypes = array_unique(array_merge(
    //         config('speech.allowed_mime_types', []),
    //         ['video/webm', 'video/ogg', 'application/octet-stream'],
    //     ));

    //     return [
    //         'audio' => ['required', 'file', 'max:'.$maxKb, 'mimetypes:'.implode(',', $mimeTypes)],
    //     ];
    // }

    // /** Container mime normalised to the audio equivalent the speech driver expects. */
    // public function audioMimeType(): string
    // {
    //     return match ($this->file('audio')?->getMimeType()) {
    //         'video/webm' => 'audio/webm',
    //         'video/ogg' => 'audio/ogg',
    //         'application/octet-stream', null => 'audio/webm',
    //         default => $this->file('audio')->getMimeType(),
    //     };
    // }
}
