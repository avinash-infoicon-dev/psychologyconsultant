<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ContactInquiry extends Model
{
    protected $fillable = [
        'uuid',
        'name',
        'email',
        'phone',
        'consultation_type',
        'message',
        'source',
        'page_url',
        'locale',
        'ip_address',
        'mailed_at',
    ];

    protected function casts(): array
    {
        return [
            'mailed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ContactInquiry $inquiry) {
            if (empty($inquiry->uuid)) {
                $inquiry->uuid = (string) Str::uuid();
            }
        });
    }
}
