<?php

namespace App\Traits;

use App\Support\AuditLogger;

/**
 * Attach directly to a model to get automatic before/after tracking in
 * the audit_logs table on create/update/delete. For models you'd rather
 * not modify directly, use app/Observers/AuditObserver.php instead,
 * registered per config('security.audit.log_models') — both mechanisms
 * share the same underlying logic in App\Support\AuditLogger.
 */
trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(fn ($model) => AuditLogger::logCreated($model));
        static::updated(fn ($model) => AuditLogger::logUpdated($model));
        static::deleted(fn ($model) => AuditLogger::logDeleted($model));
    }
}
