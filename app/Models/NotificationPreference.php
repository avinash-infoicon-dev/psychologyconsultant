<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    protected $fillable = [
        'user_id',
        'new_message_in_app',
        'new_message_email',
        'subscription_in_app',
        'subscription_email',
        'exchange_in_app',
        'exchange_email',
        'account_in_app',
        'account_email',
        'marketing_in_app',
        'marketing_email',
    ];

    protected function casts(): array
    {
        return [
            'new_message_in_app' => 'boolean',
            'new_message_email' => 'boolean',
            'subscription_in_app' => 'boolean',
            'subscription_email' => 'boolean',
            'exchange_in_app' => 'boolean',
            'exchange_email' => 'boolean',
            'account_in_app' => 'boolean',
            'account_email' => 'boolean',
            'marketing_in_app' => 'boolean',
            'marketing_email' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function defaults(): array
    {
        return [
            'new_message_in_app' => true,
            'new_message_email' => true,
            'subscription_in_app' => true,
            'subscription_email' => true,
            'exchange_in_app' => true,
            'exchange_email' => true,
            'account_in_app' => true,
            'account_email' => true,
            'marketing_in_app' => false,
            'marketing_email' => false,
        ];
    }
}
