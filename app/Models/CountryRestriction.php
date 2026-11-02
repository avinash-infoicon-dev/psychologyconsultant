<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryRestriction extends Model
{
    protected $fillable = ['country_code', 'country_name', 'status', 'reason'];

    public function isBlocked(): bool
    {
        return $this->status === 'blocked';
    }

    public function isRestricted(): bool
    {
        return $this->status === 'restricted';
    }

    public function scopeAllowed($query)
    {
        return $query->where('status', 'allowed');
    }

    /**
     * Allowed countries as code => name, ordered by name.
     *
     * @return \Illuminate\Support\Collection<string, string>
     */
    public static function allowedOptions()
    {
        return static::query()
            ->allowed()
            ->orderBy('country_name')
            ->pluck('country_name', 'country_code');
    }

    public static function statusFor(string $countryCode): string
    {
        //return static::where('country_code', strtoupper($countryCode))->value('status') ?? 'allowed';
        return static::where('country_code', strtoupper($countryCode))->value('status') ?? 'blocked';
    }
}
