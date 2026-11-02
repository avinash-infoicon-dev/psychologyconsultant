<?php

return [
    'quota_exceeded' => 'You have no exchanges remaining on your current subscription.',
    'exchange_message_limit' => 'You have reached the maximum number of messages for this exchange.',
    'your_messages' => 'Your messages: :used / :limit',
    'messages_remaining' => ':count remaining',
    'composer_disabled_exchange_limit' => 'You cannot send more messages in this exchange.',
    'psychologist_unavailable' => 'Your psychologist is currently unavailable.',
    'channel_not_allowed' => 'This message type is not included in your current plan.',
    'channel_not_allowed_audio' => 'Voice messages are not included in your current access (text + dictation only). Upgrade to Standard/Intensive or use a plan that includes audio.',
    'channel_not_allowed_text' => 'Text messages are not included in your current plan.',
    'channel_not_allowed_dictation' => 'Dictation is not included in your current plan.',
    'sent' => 'Message sent successfully.',
    'audio_sent' => 'Voice message sent successfully.',
    'marked_read' => 'Message marked as read.',

    'validation' => [
        'attributes' => [
            'body' => 'message',
            'audio' => 'audio file',
            'duration_seconds' => 'audio duration',
            'page' => 'page',
            'per_page' => 'results per page',
        ],
        'messages' => [
            'body.required' => 'Please enter a message.',
            'body.string' => 'The message must be valid text.',
            'body.max' => 'The message may not be greater than :max characters.',
            'duration_seconds.required' => 'Audio duration is required.',
            'duration_seconds.max' => 'Audio messages may not be longer than :max seconds.',
            'audio.required' => 'Please select an audio file to upload.',
            'audio.file' => 'The audio must be a valid file.',
            'audio.max' => 'The audio file may not be greater than :max kilobytes.',
            'audio.mimetypes' => 'The audio file must be one of the following types: :values.',
            'audio.mimes' => 'The audio file must be one of the following formats: :values.',
            'page.integer' => 'The page must be a number.',
            'page.min' => 'The page must be at least :min.',
            'per_page.integer' => 'Results per page must be a number.',
            'per_page.min' => 'Results per page must be at least :min.',
            'per_page.max' => 'Results per page may not be greater than :max.',
        ],
    ],
];
