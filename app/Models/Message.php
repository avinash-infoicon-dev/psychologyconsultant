<?php

namespace App\Models;

use App\Models\Concerns\DecryptsAttributesSafely;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Message extends Model
{
    use DecryptsAttributesSafely;
    use HasFactory;

    protected static bool $loggedUndecryptableAttribute = false;

    protected $fillable = ['conversation_id', 'sender_id', 'type', 'body', 'is_read', 'read_at'];

    protected function casts(): array
    {
        return [
            // Encrypted at rest — see config/security.php `encrypted_fields`.
            'body' => 'encrypted',
            'is_read' => 'boolean',
            'read_at' => 'datetime',
            'edited_at' => 'datetime',
            'deleted_for_sender_at' => 'datetime',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function audio(): HasOne
    {
        return $this->hasOne(AudioMessage::class);
    }

    public function isAudio(): bool
    {
        return $this->type === 'audio';
    }

    public function markAsRead(): void
    {
        if (! $this->is_read) {
            $this->update(['is_read' => true, 'read_at' => now()]);
        }
    }
}
