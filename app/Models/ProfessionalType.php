<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProfessionalType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'status',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function professionals(): HasMany
    {
        return $this->hasMany(Professional::class);
    }
}
