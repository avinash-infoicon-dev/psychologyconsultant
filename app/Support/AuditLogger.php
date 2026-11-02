<?php

namespace App\Support;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

/**
 * Two ways to opt a model into audit logging, both backed by this same
 * logic:
 *   1. `use Auditable;` on the model itself (see app/Traits/Auditable.php)
 *      — used where the model's own class is the natural place to
 *      declare it (User, ClinicalNote).
 *   2. Registered as an Observer in AppServiceProvider for every model
 *      listed in config('security.audit.log_models') that doesn't
 *      already use the trait — see app/Observers/AuditObserver.php.
 *      This is the mechanism for models you don't want to touch
 *      directly, or want to enable/disable purely via config.
 */
class AuditLogger
{
    public static function logCreated(Model $model): void
    {
        static::write($model, 'created', null, $model->getAttributes());
    }

    public static function logUpdated(Model $model): void
    {
        static::write($model, 'updated', $model->getOriginal(), $model->getChanges());
    }

    public static function logDeleted(Model $model): void
    {
        static::write($model, 'deleted', $model->getAttributes(), null);
    }

    protected static function write(Model $model, string $event, ?array $old, ?array $new): void
    {
        if (! config('security.audit.enabled', true)) {
            return;
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'auditable_type' => $model->getMorphClass(),
            'auditable_id' => $model->getKey(),
            'event' => $event,
            'old_values' => static::redactSensitive($old),
            'new_values' => static::redactSensitive($new),
            'ip_address' => request()?->ip(),
        ]);
    }

    protected static function redactSensitive(?array $attributes): ?array
    {
        if ($attributes === null) {
            return null;
        }

        $redactedKeys = ['password', 'content', 'body', 'transcript', 'two_factor_secret'];

        foreach ($redactedKeys as $key) {
            if (array_key_exists($key, $attributes)) {
                $attributes[$key] = '[redacted]';
            }
        }

        return $attributes;
    }
}
