<?php

namespace Tests\Unit;

use App\Support\Locales;
use Illuminate\Http\Request;
use Tests\TestCase;

class LocalesErrorPageTest extends TestCase
{
    public function test_from_accept_language_maps_french_to_supported_code(): void
    {
        $this->assertSame('fr_CH', Locales::fromAcceptLanguage('fr-CH,fr;q=0.9,en;q=0.8'));
        $this->assertSame('en', Locales::fromAcceptLanguage('en-US,en;q=0.9'));
        $this->assertNull(Locales::fromAcceptLanguage('es-ES,es;q=0.9'));
    }

    public function test_apply_from_request_prefers_locale_query(): void
    {
        $request = Request::create('/maintenance', 'GET', ['locale' => 'en']);

        $this->assertSame('en', Locales::applyFromRequest($request, true));
        $this->assertSame('en', $this->app->getLocale());
    }
}
