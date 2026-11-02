<?php

namespace App\Services\Cms;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CmsUploadService
{
    public function store(string $directory, UploadedFile $file): array
    {
        $stored = $file->store($directory, 'public');

        return [
            'path' => $stored,
            'original_name' => $file->getClientOriginalName(),
        ];
    }

    public function delete(?string $path, string $managedPrefix): void
    {
        if (! $path || ! str_starts_with($path, $managedPrefix)) {
            return;
        }

        Storage::disk('public')->delete($path);
    }

    public function deleteDirectory(string $directory): void
    {
        Storage::disk('public')->deleteDirectory($directory);
    }
}
