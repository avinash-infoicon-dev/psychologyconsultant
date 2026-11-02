<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::routes(['middleware' => ['web', 'auth']]);

Broadcast::channel('App.Models.User.{id}', function ($user, int $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('conversation.{uuid}', function ($user, string $uuid) {
    $conversation = Conversation::query()
        ->with(['patient', 'psychologist', 'psychologist.user'])
        ->where('uuid', $uuid)
        ->first();

    if (! $conversation) {
        return false;
    }

    $user->loadMissing(['patient', 'psychologist', 'professional']);

    if ($user->isPatient()) {
        return (int) $conversation->patient_id === (int) $user->patient?->id;
    }

    if ($user->isPsychologist()) {
        $professionalId = $user->psychologist?->id ?? $user->professional?->id;

        return (int) $conversation->psychologist_id === (int) $professionalId;
    }

    return false;
});

// Presence: users currently connected via Echo/Reverb (chat Online status).
Broadcast::channel('online', function ($user) {
    return [
        'id' => (int) $user->id,
        'name' => $user->name,
    ];
});
