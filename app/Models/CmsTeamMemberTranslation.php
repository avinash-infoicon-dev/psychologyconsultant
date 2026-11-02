<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CmsTeamMemberTranslation extends Model
{
    protected $fillable = [
        'cms_team_member_id',
        'locale',
        'name',
        'role',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(CmsTeamMember::class, 'cms_team_member_id');
    }
}
