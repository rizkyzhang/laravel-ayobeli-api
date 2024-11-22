<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use SodiumException;

class VerifyRequestSignature
{
    public function handle(Request $request, Closure $next)
    {
        $clientSignature = $request->header('X-Signature');
        $timestamp = $request->header('X-Timestamp');
        $method = $request->method();
        $url = $request->fullUrl();
        $requestBodyString = $request->getContent();

        // Check if any required header is empty
        if (empty($clientSignature) || empty($timestamp)) {
            return response()->json(['error' => 'Missing required signature headers'], 401);
        }

        // Combine timestamp, method, URL, and body
        $message = $timestamp . $method . $url . $requestBodyString;

        $clientPublicKey = base64_decode(getenv('CLIENT_PUBLIC_KEY'));
        $serverPrivateKey = base64_decode(getenv('SERVER_PRIVATE_KEY'));
        try {
            $sharedSecret = sodium_crypto_scalarmult($serverPrivateKey, $clientPublicKey);
        } catch (SodiumException $e) {
            return response()->json(['error' => 'Internal server error'], 500);
        }
        $serverSignature = hash_hmac('sha256', $message, $sharedSecret);

        if ($clientSignature !== $serverSignature) {
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        return $next($request);
    }
}
