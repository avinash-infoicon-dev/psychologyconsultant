<?php

namespace App\Http\Middleware;

use App\Support\Locales;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetApiLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->query('locale')
            ?? Locales::fromAcceptLanguage($request->header('Accept-Language'))
            ?? config('app.locale');

        if (! Locales::isSupported($locale)) {
            $locale = config('app.locale');
        }

        App::setLocale($locale);

        return $next($request);
    }

    // Moved to Locales::fromAcceptLanguage().
    // protected function fromAcceptLanguage(?string $header): ?string
    // {
    //     if (! $header) {
    //         return null;
    //     }
    //
    //     foreach (explode(',', $header) as $part) {
    //         $tag = strtolower(trim(explode(';', $part)[0]));
    //
    //         if (Locales::isSupported($tag)) {
    //             return $tag;
    //         }
    //
    //         $short = strtok($tag, '-');
    //
    //         foreach (Locales::codes() as $code) {
    //             if (str_starts_with(strtolower($code), $short)) {
    //                 return $code;
    //             }
    //         }
    //     }
    //
    //     return null;
    // }
}
