<?php

namespace App\Services\Cms;

use App\Models\HomeSlider;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HomeSliderImageService
{
    public function store(HomeSlider $slider, string $locale, UploadedFile $file): array
    {
        $stored = $file->store('home-sliders/'.$slider->id.'/'.$locale, 'public');

        return [
            'image_path' => $stored,
            'image_original_name' => $file->getClientOriginalName(),
        ];
    }

    public function delete(?string $path): void
    {
        if (! $this->isManagedPath($path)) {
            return;
        }

        Storage::disk('public')->delete($path);
    }

    public function deleteForSlider(HomeSlider $slider): void
    {
        $slider->loadMissing('translations');
        foreach ($slider->translations as $translation) {
            $this->delete($translation->image_path);
        }
        Storage::disk('public')->deleteDirectory('home-sliders/'.$slider->id);
    }

    public function isManagedPath(?string $path): bool
    {
        if (! $path) {
            return false;
        }

        if (Str::startsWith($path, ['http://', 'https://', '//', 'frontendassets/', 'assets/'])) {
            return false;
        }

        return Str::startsWith($path, 'home-sliders/');
    }
}
