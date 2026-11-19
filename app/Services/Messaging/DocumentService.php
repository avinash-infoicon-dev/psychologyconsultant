<?php

namespace App\Services\Messaging;

use App\Models\DocumentMessage;
use App\Models\Message;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentService
{
    public function store(Message $message, UploadedFile $file): DocumentMessage
    {
        $disk = Storage::disk(config('speech.storage_disk', 'private'));
        $filename = 'documents/'.Str::uuid().'.enc';
        $contents = file_get_contents($file->getRealPath());
        $encrypted = config('security.audio_encryption.enabled', true);

        $disk->put($filename, $encrypted ? Crypt::encrypt($contents) : $contents);

        return DocumentMessage::create([
            'message_id' => $message->id,
            'file_path' => $filename,
            'original_name' => $this->safeName($file),
            'mime_type' => 'application/pdf',
            'size_bytes' => $file->getSize(),
            'is_encrypted' => $encrypted,
        ]);
    }

    public function retrieveBytes(DocumentMessage $document): string
    {
        $disk = Storage::disk(config('speech.storage_disk', 'private'));
        $raw = $disk->get($document->file_path);

        return $document->is_encrypted ? Crypt::decrypt($raw) : $raw;
    }

    protected function safeName(UploadedFile $file): string
    {
        $name = basename(str_replace('\\', '/', (string) $file->getClientOriginalName()));
        $name = preg_replace('/[^\pL\pN.\- ()]+/u', '_', $name) ?: 'document.pdf';

        if (! str_ends_with(strtolower($name), '.pdf')) {
            $name .= '.pdf';
        }

        return mb_substr($name, 0, 180);
    }
}
