<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

/**
 * Cleans up orphaned temp files (e.g. NativeSpeechService's scratch
 * files, in case a job died before its `finally` block ran) and
 * anything sitting in private storage past its retention window.
 * Scheduled daily in routes/console.php.
 */
class DeleteExpiredFiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $tmpDisk = Storage::disk('local');

        if ($tmpDisk->exists('tmp')) {
            foreach ($tmpDisk->files('tmp') as $file) {
                if ($tmpDisk->lastModified($file) < now()->subDay()->timestamp) {
                    $tmpDisk->delete($file);
                }
            }
        }
    }
}
