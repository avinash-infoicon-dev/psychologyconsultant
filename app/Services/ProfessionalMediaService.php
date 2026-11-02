<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfessionalMediaService
{
    public function storePublicImage(UploadedFile $file, int $professionalId, string $folder): string
    {
        $path = $file->store(
            'professionals/'.$professionalId.'/'.$folder,
            'public'
        );

        // Store as storage/... so existing asset() URLs keep working.
        return 'storage/'.$path;
    }

    public function storePrivateDocument(UploadedFile $file, int $professionalId): string
    {
        return $file->store(
            'documents/professionals/'.$professionalId,
            'private'
        );
    }

    public function deletePublicPath(?string $path): void
    {
        if (! $path || Str::startsWith($path, ['http://', 'https://', '//'])) {
            return;
        }

        $relative = Str::startsWith($path, 'storage/')
            ? Str::after($path, 'storage/')
            : ltrim($path, '/');

        Storage::disk('public')->delete($relative);
    }

    public function deletePrivatePath(?string $path): void
    {
        if (! $path || Str::startsWith($path, ['http://', 'https://', '//'])) {
            return;
        }

        Storage::disk('private')->delete($path);
    }
}
