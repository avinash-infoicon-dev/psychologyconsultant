<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    public const TYPE_CONVERSATION = 'conversation';

    public const TYPE_USER = 'user';

    public const TYPES = [
        self::TYPE_CONVERSATION,
        self::TYPE_USER,
    ];

    public const REASONS = [
        'inappropriate_behavior',
        'harassment',
        'inappropriate_content',
        'other',
    ];

    public const STATUS_OPEN = 'open';

    public const STATUS_IN_REVIEW = 'in_review';

    public const STATUS_RESOLVED = 'resolved';

    public const STATUS_CLOSED = 'closed';

    public const STATUSES = [
        self::STATUS_OPEN,
        self::STATUS_IN_REVIEW,
        self::STATUS_RESOLVED,
        self::STATUS_CLOSED,
    ];

    protected $fillable = [
        'reporter_id',
        'reported_user_id',
        'conversation_id',
        'message_id',
        'type',
        'reason',
        'description',
        'status',
        'assigned_to',
        'resolved_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function reportedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ReportComment::class)->oldest();
    }

    public function hasReportedThread(): bool
    {
        return $this->comments()
            ->where('audience', ReportComment::AUDIENCE_REPORTED)
            ->exists();
    }

    public function isReporter(User $user): bool
    {
        return $this->reporter_id === $user->id;
    }

    public function isReportedUser(User $user): bool
    {
        return $this->reported_user_id === $user->id;
    }

    public function isClosed(): bool
    {
        return $this->status === self::STATUS_CLOSED;
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_OPEN => 'is-open',
            self::STATUS_IN_REVIEW => 'is-review',
            self::STATUS_RESOLVED => 'is-resolved',
            self::STATUS_CLOSED => 'is-closed',
            default => 'is-closed',
        };
    }

    public function adminBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_OPEN => 'text-bg-danger',
            self::STATUS_IN_REVIEW => 'text-bg-warning',
            self::STATUS_RESOLVED => 'text-bg-success',
            self::STATUS_CLOSED => 'text-bg-secondary',
            default => 'text-bg-secondary',
        };
    }
}
