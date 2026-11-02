<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

/**
 * Shells out to mysqldump rather than pulling in spatie/laravel-backup,
 * to keep this scaffold dependency-free. For production, spatie's
 * package (S3 offsite storage, backup rotation, notifications) is
 * worth the dependency — swap this job's body for `Artisan::call('backup:run')`
 * once it's installed.
 */
class BackupDatabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $connection = config('database.default');
        $db = config("database.connections.{$connection}");

        $filename = 'backup_'.now()->format('Y_m_d_His').'.sql';
        $path = storage_path("app/backups/{$filename}");

        @mkdir(dirname($path), 0755, true);

        $result = Process::run([
            'mysqldump',
            '-h', $db['host'],
            '-u', $db['username'],
            '-p'.$db['password'],
            $db['database'],
        ]);

        if (! $result->successful()) {
            Log::error('Database backup failed', ['error' => $result->errorOutput()]);

            return;
        }

        file_put_contents($path, $result->output());
    }
}
