<?php

namespace Tests\Unit;

use App\Models\Notification;
use PHPUnit\Framework\TestCase;

class NotificationActionUrlTest extends TestCase
{
    public function test_absolute_notification_urls_are_resolved_on_the_current_dashboard_host(): void
    {
        $notification = new Notification([
            'data' => ['url' => 'https://old.example.test/patient/subscription?tab=history#current'],
        ]);

        $this->assertSame(
            '/patient/subscription?tab=history#current',
            $notification->actionUrl(),
        );
    }

    public function test_invalid_notification_urls_are_not_openable(): void
    {
        $notification = new Notification([
            'data' => ['url' => 'javascript:alert(1)'],
        ]);

        $this->assertNull($notification->actionUrl());
    }
}
