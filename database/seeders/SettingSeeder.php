<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::set(
            'security.two_factor.enabled',
            config('security.two_factor.enabled', true),
            'boolean',
            'security',
        );

        Setting::set(
            'security.two_factor.required_for_roles',
            config('security.two_factor.required_for_roles', []),
            'json',
            'security',
        );

        // §5.5 exchange-unit limits — per role (0 = unlimited).
        // Prefer existing legacy patient values when present.
        $patientText = Setting::get(
            'messaging.patient.max_text_chars',
            Setting::get('messaging.max_text_chars', config('messaging.patient.max_text_chars', 2000))
        );
        $patientAudio = Setting::get(
            'messaging.patient.max_audio_seconds',
            Setting::get('messaging.max_audio_seconds', config('messaging.patient.max_audio_seconds', 60))
        );

        Setting::set('messaging.patient.max_text_chars', (int) $patientText, 'integer', 'messaging');
        Setting::set('messaging.patient.max_audio_seconds', (int) $patientAudio, 'integer', 'messaging');
        Setting::set(
            'messaging.psychologist.max_text_chars',
            (int) Setting::get(
                'messaging.psychologist.max_text_chars',
                config('messaging.psychologist.max_text_chars', 0)
            ),
            'integer',
            'messaging',
        );
        Setting::set(
            'messaging.psychologist.max_audio_seconds',
            (int) Setting::get(
                'messaging.psychologist.max_audio_seconds',
                config('messaging.psychologist.max_audio_seconds', 0)
            ),
            'integer',
            'messaging',
        );

        // Keep legacy flat keys aligned with patient limits.
        Setting::set('messaging.max_text_chars', (int) $patientText, 'integer', 'messaging');
        Setting::set('messaging.max_audio_seconds', (int) $patientAudio, 'integer', 'messaging');

        $audioKb = Setting::get(
            'messaging.audio.max_upload_size_kb',
            config('messaging.audio.max_upload_size_kb', config('speech.max_upload_size_kb', 25600))
        );
        Setting::set('messaging.audio.max_upload_size_kb', (int) $audioKb, 'integer', 'messaging');

        $audioFormats = Setting::get(
            'messaging.audio.allowed_formats',
            config('messaging.audio.allowed_formats', ['webm', 'ogg', 'mp3', 'wav', 'm4a'])
        );
        if (! is_array($audioFormats) || $audioFormats === []) {
            $audioFormats = ['webm', 'ogg', 'mp3', 'wav', 'm4a'];
        }
        Setting::set('messaging.audio.allowed_formats', $audioFormats, 'json', 'messaging');

        Setting::set(
            'messaging.patient.messages_per_exchange',
            (int) Setting::get(
                'messaging.patient.messages_per_exchange',
                config('messaging.messages_per_exchange.patient')
            ),
            'integer',
            'messaging',
        );
        Setting::set(
            'messaging.psychologist.messages_per_exchange',
            (int) Setting::get(
                'messaging.psychologist.messages_per_exchange',
                config('messaging.messages_per_exchange.psychologist')
            ),
            'integer',
            'messaging',
        );
    }
}
