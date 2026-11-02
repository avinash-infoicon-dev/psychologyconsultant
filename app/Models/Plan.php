<?php

namespace App\Models;

use App\Models\Concerns\HasCmsTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;
    use HasCmsTranslations;

    public const TYPE_SUBSCRIPTION = 'subscription';

    public const TYPE_ON_DEMAND = 'on_demand';

    public const TYPE_ADDON = 'addon';

    public const CHANNEL_TEXT = 'text';

    public const CHANNEL_DICTATION = 'dictation';

    public const CHANNEL_AUDIO = 'audio';

    public const STATUS_DRAFT = 'draft';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'name', 'slug', 'plan_type', 'description', 'motto', 'price', 'currency',
        'billing_interval', 'exchange_quota',
        'max_text_chars', 'max_audio_seconds',
        'patient_messages_per_exchange', 'psychologist_messages_per_exchange',
        'response_time_hours', 'response_time_label', 'channels',
        'features',
        'stripe_price_id', 'plan_id', 'is_active', 'status', 'is_recommended', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'channels' => 'array',
            'is_active' => 'boolean',
            'is_recommended' => 'boolean',
            'response_time_hours' => 'integer',
        ];
    }

    public function translations(): HasMany
    {
        return $this->hasMany(PlanTranslation::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function getNameAttribute(?string $value): ?string
    {
        return $this->translatedValue('name', $value);
    }

    public function getMottoAttribute(?string $value): ?string
    {
        return $this->translatedValue('motto', $value);
    }

    public function getDescriptionAttribute(?string $value): ?string
    {
        return $this->translatedValue('description', $value);
    }

    public function getResponseTimeLabelAttribute(?string $value): ?string
    {
        return $this->translatedValue('response_time_label', $value);
    }

    public function getFeaturesAttribute(mixed $value): ?array
    {
        $translated = $this->translate()?->features;
        if (is_array($translated) && $translated !== []) {
            return $translated;
        }

        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);

            return is_array($decoded) ? $decoded : null;
        }

        return null;
    }

    /** Price formatted as a decimal string, e.g. 49.00 */
    public function displayPrice(): string
    {
        return number_format($this->price / 100, 2);
    }

    public function isSubscription(): bool
    {
        return $this->plan_type === self::TYPE_SUBSCRIPTION;
    }

    public function isPerExchange(): bool
    {
        return $this->billing_interval === 'per_exchange'
            || in_array($this->plan_type, [self::TYPE_ON_DEMAND, self::TYPE_ADDON], true);
    }

    public function allowsChannel(string $channel): bool
    {
        $channels = $this->channels ?? [];

        if ($channels !== []) {
            return in_array($channel, $channels, true);
        }

        // Legacy features JSON fallback (pre-§6.3 plans).
        $features = $this->features ?? [];

        return match ($channel) {
            self::CHANNEL_AUDIO => in_array('audio_messaging', $features, true),
            self::CHANNEL_TEXT, self::CHANNEL_DICTATION => in_array('text_messaging', $features, true)
                || in_array('audio_messaging', $features, true),
            default => false,
        };
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isArchived(): bool
    {
        return $this->status === self::STATUS_ARCHIVED;
    }

    public function isSellable(): bool
    {
        return $this->status === self::STATUS_ACTIVE && $this->is_active;
    }

    /** Soft-retire a plan without deleting subscription history. */
    public function archive(): void
    {
        $this->update([
            'status' => self::STATUS_ARCHIVED,
            'is_active' => false,
        ]);
    }

    public function toSnapshot(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'plan_type' => $this->plan_type,
            'description' => $this->description,
            'motto' => $this->motto,
            'price' => $this->price,
            'currency' => $this->currency,
            'billing_interval' => $this->billing_interval,
            'exchange_quota' => $this->exchange_quota,
            'max_text_chars' => $this->max_text_chars,
            'max_audio_seconds' => $this->max_audio_seconds,
            'patient_messages_per_exchange' => $this->patient_messages_per_exchange,
            'psychologist_messages_per_exchange' => $this->psychologist_messages_per_exchange,
            'response_time_hours' => $this->response_time_hours,
            'response_time_label' => $this->response_time_label,
            'channels' => $this->channels,
            'features' => $this->features,
            'stripe_price_id' => $this->stripe_price_id,
            'plan_id' => $this->plan_id,
            'is_active' => $this->is_active,
            'status' => $this->status,
            'is_recommended' => $this->is_recommended,
            'sort_order' => $this->sort_order,
        ];
    }

    protected function translatedValue(string $field, mixed $fallback): mixed
    {
        $translated = $this->translate()?->{$field} ?? null;

        if ($translated === null || $translated === '') {
            return $fallback;
        }

        return $translated;
    }
}
