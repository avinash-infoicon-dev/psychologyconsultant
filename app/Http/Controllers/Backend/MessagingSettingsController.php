<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\MessagingLimits;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessagingSettingsController extends Controller
{
    public function edit(): View
    {
        $limits = [
            'patient' => [
                'max_text_chars' => MessagingLimits::configuredLimit(
                    MessagingLimits::ROLE_PATIENT,
                    'max_text_chars'
                ),
                'max_audio_seconds' => MessagingLimits::configuredLimit(
                    MessagingLimits::ROLE_PATIENT,
                    'max_audio_seconds'
                ),
                'messages_per_exchange' => MessagingLimits::configuredMessagesPerExchange(
                    MessagingLimits::ROLE_PATIENT
                ),
            ],
            'psychologist' => [
                'max_text_chars' => MessagingLimits::configuredLimit(
                    MessagingLimits::ROLE_PSYCHOLOGIST,
                    'max_text_chars'
                ),
                'max_audio_seconds' => MessagingLimits::configuredLimit(
                    MessagingLimits::ROLE_PSYCHOLOGIST,
                    'max_audio_seconds'
                ),
                'messages_per_exchange' => MessagingLimits::configuredMessagesPerExchange(
                    MessagingLimits::ROLE_PSYCHOLOGIST
                ),
            ],
        ];

        $audio = [
            'max_upload_mb' => MessagingLimits::maxAudioUploadMb(),
            'formats' => MessagingLimits::audioFormatCatalog(),
            'selected_formats' => MessagingLimits::configuredAudioFormatKeys(),
        ];

        return view('backend.settings.messaging', compact('limits', 'audio'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            // 'patient_max_text_chars' => ['required', 'integer', 'min:0', 'max:100000'],
            // 'patient_max_audio_seconds' => ['required', 'integer', 'min:0', 'max:3600'],
            // 'psychologist_max_text_chars' => ['required', 'integer', 'min:0', 'max:100000'],
            // 'psychologist_max_audio_seconds' => ['required', 'integer', 'min:0', 'max:3600'],
            // 'patient_messages_per_exchange' => ['required', 'integer', 'min:0'],
            // 'psychologist_messages_per_exchange' => ['required', 'integer', 'min:0'],
            'audio_max_upload_mb' => ['required', 'integer', 'min:1', 'max:100'],
            'audio_formats' => ['required', 'array', 'min:1'],
            'audio_formats.*' => ['string', 'in:'.implode(',', array_keys(MessagingLimits::audioFormatCatalog()))],
        ]);

        // Setting::set(
        //     'messaging.patient.max_text_chars',
        //     (int) $data['patient_max_text_chars'],
        //     'integer',
        //     'messaging',
        // );
        // Setting::set(
        //     'messaging.patient.max_audio_seconds',
        //     (int) $data['patient_max_audio_seconds'],
        //     'integer',
        //     'messaging',
        // );
        // Setting::set(
        //     'messaging.psychologist.max_text_chars',
        //     (int) $data['psychologist_max_text_chars'],
        //     'integer',
        //     'messaging',
        // );
        // Setting::set(
        //     'messaging.psychologist.max_audio_seconds',
        //     (int) $data['psychologist_max_audio_seconds'],
        //     'integer',
        //     'messaging',
        // );

        // // Keep legacy keys in sync with patient limits for older readers.
        // Setting::set(
        //     'messaging.max_text_chars',
        //     (int) $data['patient_max_text_chars'],
        //     'integer',
        //     'messaging',
        // );
        // Setting::set(
        //     'messaging.max_audio_seconds',
        //     (int) $data['patient_max_audio_seconds'],
        //     'integer',
        //     'messaging',
        // );

        // Setting::set(
        //     'messaging.patient.messages_per_exchange',
        //     (int) $data['patient_messages_per_exchange'],
        //     'integer',
        //     'messaging',
        // );
        // Setting::set(
        //     'messaging.psychologist.messages_per_exchange',
        //     (int) $data['psychologist_messages_per_exchange'],
        //     'integer',
        //     'messaging',
        // );

        $formatKeys = array_values(array_unique($data['audio_formats']));
        Setting::set(
            'messaging.audio.max_upload_size_kb',
            (int) $data['audio_max_upload_mb'] * 1024,
            'integer',
            'messaging',
        );
        Setting::set(
            'messaging.audio.allowed_formats',
            $formatKeys,
            'json',
            'messaging',
        );

        return redirect()
            ->route('admin.settings.messaging')
            ->with('success', __('backend.settings.messaging.updated'));
    }
}
