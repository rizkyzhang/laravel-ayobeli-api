<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerifyRequest
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
             return response()->json(['error' => 'Missing required headers'], 400);
         }

         // Combine timestamp, method, URL, and body
         $message = $timestamp . $method . $url. $requestBodyString;

         $clientPublicKey = base64_decode(getenv('CLIENT_PUBLIC_KEY'));
         $serverPrivateKey = base64_decode(getenv('SERVER_PRIVATE_KEY'));

         $sharedSecret = sodium_crypto_scalarmult($serverPrivateKey, $clientPublicKey);
         $serverSignature = hash_hmac('sha256', $message, $sharedSecret);

         if ($clientSignature !== $serverSignature) {
             return response()->json(['error' => 'Invalid signature', 'serverSignature' => $serverSignature, 'clientSignature' => $clientSignature, 'message' => $message, 'body' => $requestBodyString], 401);
         }

         $request->attributes->set('clientSignature', $clientSignature);
         $request->attributes->set('serverSignature', $serverSignature);
         $request->attributes->set('message', $message);
         $request->attributes->set('body',  $requestBodyString);

        return $next($request);
    }
}
