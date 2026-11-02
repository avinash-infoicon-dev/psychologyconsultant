<?php

namespace App\Http\Middleware;

use App\Models\CountryRestriction;
use App\Services\GeoLocationService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applied globally to the api middleware group (see
 * bootstrap-app-additions.php). Resolves the visitor's country once per
 * request and:
 *   - 'blocked'    -> 403, request never reaches the controller
 *   - 'restricted' -> allowed through, but geo_status is attached to the
 *                     request so a controller/policy can apply narrower
 *                     rules if it needs to (e.g. read-only access)
 *   - unresolved IP (local/dev, lookup failure) -> fails open, allowed
 *     through unflagged, since blocking on an inconclusive geolocation
 *     would lock out real users over a transient lookup failure
 */
class CheckCountryRestriction
{
    public function __construct(protected GeoLocationService $geo) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! config('healthcare.compliance.require_country_restriction_check', true)) {
            return $next($request);
        }
        $country = $this->geo->resolveCountry($request->ip());
        //$country = $this->geo->resolveCountry('8.8.8.8');

        // dd(
        //         config('geolocation.maxmind.database_path'),
        //         file_exists(config('geolocation.maxmind.database_path')),
        //         is_readable(config('geolocation.maxmind.database_path'))
        //     );


        //dd(class_exists(\GeoIp2\Database\Reader::class));
        //dd($this->geo->resolveCountry('8.8.8.8'));
        //dd($request->ip());
        //dd($country);
        if ($country === null) {
            return $next($request);
        }
        $status = CountryRestriction::statusFor($country);

        if ($status === 'blocked') {
            return response()->json([
                'message' => 'This service is not available in your region.',
            ], 403);
        }

        $request->attributes->set('geo_country', $country);
        $request->attributes->set('geo_status', $status);

        return $next($request);
    }
}
