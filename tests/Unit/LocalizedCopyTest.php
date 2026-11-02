<?php

namespace Tests\Unit;

use App\Models\Message;
use App\Models\Plan;
use App\Support\AbsenceReason;
use App\Support\LocalizedNotification;
use Tests\TestCase;

class LocalizedCopyTest extends TestCase
{
    public function test_stored_english_notification_follows_the_active_locale(): void
    {
        app()->setLocale('fr_CH');

        $title = LocalizedNotification::line('New message from Ada');
        $body = LocalizedNotification::line('Your Essential subscription is now active.');

        $this->assertSame('Nouveau message de Ada', $title);
        $this->assertSame('Votre abonnement Essentiel est maintenant actif.', $body);
    }

    public function test_known_plan_names_follow_the_active_locale(): void
    {
        app()->setLocale('fr_CH');

        $this->assertSame('À la demande', Plan::localizeKnownName('On-demand'));
        $this->assertSame('Échange supplémentaire', Plan::localizeKnownName('Additional exchange'));
    }

    public function test_invalid_message_ciphertext_does_not_crash_the_reader(): void
    {
        $message = new Message;
        $message->setRawAttributes([
            'type' => 'text',
            'body' => 'not-a-valid-payload',
        ]);

        $this->assertNull($message->body);

        $fresh = new Message;
        $fresh->body = 'hello';
        $stored = $fresh->getAttributes()['body'];

        $roundTrip = new Message;
        $roundTrip->setRawAttributes(['body' => $stored, 'type' => 'text']);

        $this->assertSame('hello', $roundTrip->body);
    }

    public function test_absence_reason_codes_follow_the_active_locale(): void
    {
        app()->setLocale('fr_CH');

        $this->assertSame('Congé maladie', AbsenceReason::label('sick'));
        $this->assertSame('Congé déjà saisi', AbsenceReason::label('Congé déjà saisi'));
    }
}
