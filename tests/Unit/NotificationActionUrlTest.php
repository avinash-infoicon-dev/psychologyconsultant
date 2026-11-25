<?php

namespace Tests\Unit;

use App\Models\Notification;
use Illuminate\Http\Request;
use Tests\TestCase;

class NotificationActionUrlTest extends TestCase
{
    public function test_stale_install_prefixes_are_replaced_with_the_current_dashboard_base_path(): void
    {
        $request = Request::create('http://localhost/psychologyconsultant/public/notifications');
        $request->server->set('SCRIPT_NAME', '/psychologyconsultant/public/index.php');
        $request->server->set('PHP_SELF', '/psychologyconsultant/public/index.php');
        $request->server->set('SCRIPT_FILENAME', '/var/www/html/psychologyconsultant/public/index.php');
        $this->app->instance('request', $request);

        $notification = new Notification([
            'data' => ['url' => 'https://old.example.test/PsychologyAndLegalConsultation/public/psychologist/profile?tab=settings#current'],
        ]);

        $this->assertSame(
            '/psychologyconsultant/public/psychologist/profile?tab=settings#current',
            $notification->actionUrl(),
        );
    }

    public function test_dashboard_urls_work_when_the_application_is_installed_at_the_domain_root(): void
    {
        $request = Request::create('https://example.test/notifications');
        $this->app->instance('request', $request);

        $notification = new Notification([
            'data' => ['url' => 'http://old.example.test/PsychologyAndLegalConsultation/public/patient/subscription'],
        ]);

        $this->assertSame('/patient/subscription', $notification->actionUrl());
    }

    public function test_invalid_notification_urls_are_not_openable(): void
    {
        $notification = new Notification([
            'data' => ['url' => 'javascript:alert(1)'],
        ]);

        $this->assertNull($notification->actionUrl());
    }
}
