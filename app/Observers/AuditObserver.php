<?php

namespace App\Observers;

use App\Support\AuditLogger;
use Illuminate\Database\Eloquent\Model;

class AuditObserver
{
    public function created(Model $model): void
    {
        AuditLogger::logCreated($model);
    }

    public function updated(Model $model): void
    {
        AuditLogger::logUpdated($model);
    }

    public function deleted(Model $model): void
    {
        AuditLogger::logDeleted($model);
    }
}
