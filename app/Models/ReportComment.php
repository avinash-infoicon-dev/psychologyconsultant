<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportComment extends Model
{
    public const AUDIENCE_REPORTER = 'reporter';

    public const AUDIENCE_REPORTED = 'reported';

    public const AUDIENCE_INTERNAL = 'internal';

    public const AUDIENCES = [
        self::AUDIENCE_REPORTER,
        self::AUDIENCE_REPORTED,
        self::AUDIENCE_INTERNAL,
    ];

    protected $fillable = [
        'report_id',
        'user_id',
        'body',
        'is_internal',
        'audience',
    ];

    protected function casts(): array
    {
        return [
            'is_internal' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (ReportComment $comment) {
            if (empty($comment->audience)) {
                $comment->audience = $comment->is_internal
                    ? self::AUDIENCE_INTERNAL
                    : self::AUDIENCE_REPORTER;
            }

            $comment->is_internal = $comment->audience === self::AUDIENCE_INTERNAL;
        });
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
