<?php

namespace Tests\Feature;

use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    public function test_404_page_follows_locale_query(): void
    {
        $this->get('/this-page-does-not-exist?locale=en')
            ->assertNotFound()
            ->assertSee(__('errors.404.heading', [], 'en'), false)
            ->assertSee(__('errors.home', [], 'en'), false);

        $this->get('/this-page-does-not-exist?locale=fr_CH')
            ->assertNotFound()
            ->assertSee(__('errors.404.heading', [], 'fr_CH'), false)
            ->assertSee(__('errors.home', [], 'fr_CH'), false);
    }

    public function test_403_page_shows_specific_exception_message(): void
    {
        $html = view('errors.403', [
            'exception' => new HttpException(403, 'Your account is inactive.'),
        ])->render();

        $this->assertStringContainsString('Your account is inactive.', $html);
        $this->assertStringContainsString(__('errors.403.status'), $html);
    }

    public function test_503_page_is_translated(): void
    {
        $this->app->instance('request', Request::create('/maintenance', 'GET', ['locale' => 'en']));

        $html = html_entity_decode(view('errors.503')->with('hideHome', true)->render(), ENT_QUOTES, 'UTF-8');

        $this->assertStringContainsString(__('errors.503.heading', [], 'en'), $html);
        $this->assertStringNotContainsString(__('errors.home', [], 'en'), $html);
    }
}
