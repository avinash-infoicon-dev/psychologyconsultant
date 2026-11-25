<?php

namespace App\Models;

use App\Support\LocalizedNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Custom persisted notification record — distinct from Laravel's built-in
 * notifications table. Written to by NotificationService whenever a
 * push/email/in-app notification is dispatched, so the API has a simple
 * table to query for a user's notification center.
 */
class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'type', 'dedupe_key', 'title', 'body', 'data', 'channels', 'read_at'];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'channels' => 'array',
            'read_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function localizedTitle(): string
    {
        return LocalizedNotification::title($this);
    }

    public function localizedBody(): string
    {
        return LocalizedNotification::body($this);
    }

    public function actionUrl(): ?string
    {
        $url = $this->data['url'] ?? null;
        if (! is_string($url) || trim($url) === '') {
            return null;
        }

        $parts = parse_url($url);
        if ($parts === false || (isset($parts['scheme']) && ! in_array(strtolower($parts['scheme']), ['http', 'https'], true))) {
            return null;
        }

        $path = $parts['path'] ?? null;
        if (! is_string($path) || ! str_starts_with($path, '/')) {
            return null;
        }

        if (preg_match('#/(?:admin|patient|psychologist)(?:/|$)#', $path, $match, PREG_OFFSET_CAPTURE)) {
            $path = substr($path, $match[0][1]);
            $basePath = rtrim(request()->getBaseUrl(), '/');
            $basePath = preg_replace('#/index\.php$#', '', $basePath) ?? $basePath;
            $path = $basePath.$path;
        }

        return $path
            .(isset($parts['query']) ? '?'.$parts['query'] : '')
            .(isset($parts['fragment']) ? '#'.$parts['fragment'] : '');
    }

    public function markAsRead(): void
    {
        if (! $this->isRead()) {
            $this->update(['read_at' => now()]);
        }
    }
}
