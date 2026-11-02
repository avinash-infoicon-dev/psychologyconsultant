<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AudioMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id', 'file_path', 'mime_type', 'duration_seconds',
        'size_bytes', 'is_encrypted', 'transcript', 'language', 'transcription_status',
    ];

    protected function casts(): array
    {
        return [
            'is_encrypted' => 'boolean',
            // Transcript is sensitive clinical content — encrypt at rest.
            'transcript' => 'encrypted',
        ];
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    public function isTranscribed(): bool
    {
        return $this->transcription_status === 'completed';
    }
}
