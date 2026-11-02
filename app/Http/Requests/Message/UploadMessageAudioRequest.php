<?php

namespace App\Http\Requests\Message;

use App\Support\MessagingLimits;

class UploadMessageAudioRequest extends MessageConversationRequest
{
    public function rules(): array
    {
        // Legacy (config/speech.php only):
        // $maxKb = config('speech.max_upload_size_kb', 25600);
        $maxKb = MessagingLimits::maxAudioUploadKb();
        $maxSeconds = MessagingLimits::maxAudioSeconds($this->user());
        $mimeTypes = MessagingLimits::allowedAudioMimeTypes();
        $extensions = implode(',', MessagingLimits::allowedAudioExtensions());

        // Browser MediaRecorder uses the WebM/Ogg container; PHP finfo reports
        // those as video/webm or video/ogg even for audio-only recordings.
        // Old merge (kept for reference):
        // $mimeTypes = array_unique(array_merge(
        //     config('speech.allowed_mime_types', []),
        //     ['video/webm', 'video/ogg', 'audio/x-wav', 'audio/x-m4a', 'application/octet-stream'],
        // ));

        // Old strict list (kept for reference):
        // 'mimetypes:'.implode(',', config('speech.allowed_mime_types', [])),

        // §5.5 — per-role admin caps (null / 0 = unlimited).
        $durationRules = ['nullable', 'integer', 'min:1'];
        if ($maxSeconds !== null) {
            $durationRules = ['required', 'integer', 'min:1', 'max:'.$maxSeconds];
        }

        return [
            'audio' => [
                'required',
                'file',
                'max:'.$maxKb,
                // 'mimetypes:'.implode(',', config('speech.allowed_mime_types', [])),
                'mimetypes:'.implode(',', $mimeTypes),
                // 'mimes:webm,ogg,oga,mp3,mp4,m4a,wav',
                'mimes:'.$extensions,
            ],
            // Client-reported duration — primary server-side check for the §5.5 cap.
            'duration_seconds' => $durationRules,
        ];
    }

    /** Container mime normalised to the audio equivalent used for storage/playback. */
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
