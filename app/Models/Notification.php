<?php

namespace App\Models;

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

    public function markAsRead(): void
    {
        if (! $this->isRead()) {
            $this->update(['read_at' => now()]);
        }
    }
}
