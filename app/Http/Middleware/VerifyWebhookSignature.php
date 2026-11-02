<?php

namespace App\Http\Middleware;

use App\Services\Payment\StripeService;
use App\Services\Payment\TwintService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyWebhookSignature
{
    public function __construct(
        protected StripeService $stripe,
        protected TwintService $twint,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $payload = $request->getContent();
        $verified = match (true) {
            $request->routeIs('*.stripe') || str_contains($request->path(), 'stripe') =>
                $this->stripe->verifyWebhookSignature($payload, $request->header('Stripe-Signature', '')),
            str_contains($request->path(), 'twint') =>
                $this->twint->verifyWebhookSignature($payload, $request->header('X-Twint-Signature', '')),
            default => false,
        };

        if (! $verified) {
            return response()->json(['message' => 'Invalid webhook signature.'], 400);
        }

        return $next($request);
    }
}
