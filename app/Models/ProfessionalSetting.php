<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfessionalSetting extends Model
{
    protected $fillable = [
        'professional_id',
        'timezone',
    ];

    public function professional(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }
}
