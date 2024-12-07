<?php

// tests/Feature/Middleware/ValidateSignatureTest.php

namespace Tests\Unit\Middleware;

use App\Helpers\FakerHelper;
use App\Http\Middleware\VerifyRequestSignature;
use Closure;
use Illuminate\Http\Request;
use Tests\TestCase;


class ValidateSignatureTest extends TestCase
{
    protected Request $request;
    protected Closure $next;

    public function test_validate_signature_success()
    {
        $serverPublicKey = base64_decode(getenv('SERVER_PUBLIC_KEY'));
        $clientPrivateKey = base64_decode(getenv('CLIENT_PRIVATE_KEY'));

        $timestamp = now()->timestamp;
        $method = $this->request->method();
        $url = $this->request->fullUrl();
        $requestBodyString = $this->request->getContent();
        $message = $timestamp . $method . $url . $requestBodyString;
        $sharedSecret = sodium_crypto_scalarmult($clientPrivateKey, $serverPublicKey);
        $serverSignature = hash_hmac('sha256', $message, $sharedSecret);

        $this->request->headers->add([
            'X-Signature' => $serverSignature,
            'X-Timestamp' => $timestamp,
        ]);
        $middleware = new VerifyRequestSignature();
        $response = $middleware->handle($this->request, $this->next);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Next', $response->getContent());
    }

    public function test_validate_signature_failed()
    {
        $middleware = new VerifyRequestSignature();
        $response = $middleware->handle($this->request, $this->next);

        $this->assertEquals(401, $response->getStatusCode());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $fakerHelper = new FakerHelper();
        $method = 'POST';
        $requestBodyString = $fakerHelper->generateJsonData();

        $this->request = Request::create('/test', $method, [], [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], $requestBodyString);

        $this->next = function () {
            return response('Next');
        };
    }
}
