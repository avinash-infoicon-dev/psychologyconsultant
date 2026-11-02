<?php

namespace App\Services\Cms;

use App\Models\CmsPage;
use App\Models\CmsPageTranslation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CmsPagePdfService
{
    public function store(CmsPage $page, string $locale, UploadedFile $file): array
    {
        $stored = $file->store('cms/pages/'.$page->id, 'public');

        return [
            'pdf_path' => $stored,
            'pdf_original_name' => $file->getClientOriginalName(),
        ];
    }

    public function delete(?string $path): void
    {
        $relative = $this->relativePath($path);
        if ($relative) {
            Storage::disk('public')->delete($relative);
        }
    }

    public function deleteForPage(CmsPage $page): void
    {
        $page->loadMissing('translations');
        foreach ($page->translations as $translation) {
            $this->delete($translation->pdf_path);
        }
        Storage::disk('public')->deleteDirectory('cms/pages/'.$page->id);
    }

    public function putDummy(CmsPage $page, string $locale, string $title): array
    {
        $filename = Str::slug($page->slug.'-'.$locale).'.pdf';
        $relative = 'cms/pages/'.$page->id.'/'.$filename;
        Storage::disk('public')->put($relative, $this->minimalPdf($title));

        return [
            'pdf_path' => $relative,
            'pdf_original_name' => $filename,
        ];
    }

    public function relativePath(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return null;
        }

        return Str::startsWith($path, 'storage/')
            ? Str::after($path, 'storage/')
            : ltrim($path, '/');
    }

    protected function minimalPdf(string $title): string
    {
        $safe = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $title);
        $stream = "BT /F1 18 Tf 50 720 Td ({$safe}) Tj ET";
        $length = strlen($stream);

        return "%PDF-1.4\n"
            ."1 0 obj<< /Type /Catalog /Pages 2 0 R >>endobj\n"
            ."2 0 obj<< /Type /Pages /Kids [3 0 R] /Count 1 >>endobj\n"
            ."3 0 obj<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources<< /Font<< /F1 5 0 R >> >> >>endobj\n"
            ."4 0 obj<< /Length {$length} >>stream\n"
            .$stream."\n"
            ."endstream endobj\n"
            ."5 0 obj<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>endobj\n"
            ."trailer<< /Root 1 0 R >>\n"
            ."%%EOF\n";
    }
}
