<?php

namespace App\Services\Cms;

use App\Models\CmsSiteSetting;

class CmsSiteSettingService
{
    public function current(): CmsSiteSetting
    {
        $item = CmsSiteSetting::query()->with('translations')->first();

        if (! $item) {
            $item = CmsSiteSetting::query()->create([]);
            $item->load('translations');
        }

        return $item;
    }

    public function forget(): void
    {
        // Settings are read from the database on each request.
    }
}
