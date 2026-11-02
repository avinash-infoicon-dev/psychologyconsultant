<?php

namespace App\Services\Messaging;

use App\Events\AudioUploaded;
use App\Models\AudioMessage;
use App\Models\Message;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AudioService
{
    public function store(
        Message $message,
        UploadedFile $file,
        ?int $durationSeconds = null,
        ?string $mimeType = null,
    ): AudioMessage {
        $disk = Storage::disk(config('speech.storage_disk', 'private'));
        $directory = config('speech.audio_path', 'audio');
        $filename = $directory.'/'.Str::uuid().'.enc';

        $encrypted = config('security.audio_encryption.enabled', true)
            ? Crypt::encrypt(file_get_contents($file->getRealPath()))
            : file_get_contents($file->getRealPath());

        $disk->put($filename, $encrypted);

        $audioMessage = AudioMessage::create([
            'message_id' => $message->id,
            'file_path' => $filename,
            'mime_type' => $mimeType ?: $file->getMimeType(),
            'duration_seconds' => $durationSeconds,
            'size_bytes' => $file->getSize(),
            'is_encrypted' => config('security.audio_encryption.enabled', true),
            'transcription_status' => 'pending',
        ]);

        event(new AudioUploaded($audioMessage));

        return $audioMessage;
    }

    /** Decrypt and return raw audio bytes — used by WhisperService before transcription. */
    public function retrieveBytes(AudioMessage $audioMessage): string
    {
        $disk = Storage::disk(config('speech.storage_disk', 'private'));
        $raw = $disk->get($audioMessage->file_path);

        return $audioMessage->is_encrypted ? Crypt::decrypt($raw) : $raw;
    }

    public function delete(AudioMessage $audioMessage): void
    {
        Storage::disk(config('speech.storage_disk', 'private'))->delete($audioMessage->file_path);
    }
}
