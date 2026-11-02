<?php

namespace App\Models;

use App\Models\Concerns\DecryptsAttributesSafely;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consent extends Model
{
    use DecryptsAttributesSafely;
    use HasFactory;

    protected static bool $loggedUndecryptableAttribute = false;

    protected $fillable = [
        'user_id', 'type', 'version', 'content', 'ip_address', 'accepted_at', 'revoked_at',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'encrypted',
            'accepted_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        return $this->revoked_at === null;
    }
}
