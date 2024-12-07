<?php

namespace App\Services;

use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class AzureEmailService
{
    protected Client $client;
    protected string $endpoint;
    protected string $accessKey;
    protected string $senderAddress;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        // Get connection string from .env
        $connectionString = getenv('AZURE_COMMUNICATION_CONNECTION_STRING');

        // Parse the connection string to get endpoint and access key
        $this->parseConnectionString($connectionString);

        // Use the sender email from .env
        $this->senderAddress = getenv('AZURE_EMAIL_SENDER');  // Fetch from .env
        if (!$this->senderAddress) {
            throw new Exception('Sender email is not set in the .env file');
        }

        $this->client = new Client();
    }

    // Parse the connection string to extract endpoint and access key

    /**
     * @throws Exception
     */
    protected function parseConnectionString($connectionString): void
    {
        // Regex to extract endpoint and access key from the connection string
        preg_match('/endpoint=([^;]+)/', $connectionString, $endpointMatches);
        preg_match('/accesskey=([^;]+)/', $connectionString, $keyMatches);

        if (isset($endpointMatches[1])) {
            $this->endpoint = $endpointMatches[1]; // Extract endpoint
        } else {
            throw new Exception('Endpoint not found in connection string');
        }

        if (isset($keyMatches[1])) {
            $this->accessKey = $keyMatches[1]; // Extract access key
        } else {
            throw new Exception('AccessKey not found in connection string');
        }
    }

    // Helper function to generate the Date header

    public function sendEmail($to, $subject, $htmlContent)
    {
        $url = "/emails:send?api-version=2023-03-31"; // Set the API version
        $body = json_encode([
            "senderAddress" => $this->senderAddress, // Sender email fetched from .env
            "content" => [
                "subject" => $subject,
                "html" => $htmlContent,
//                "plainText" => $htmlContent
            ],
            "recipients" => [
                "to" => [
                    [
                        "address" => $to,
                    ],
                ],
            ],
        ]);

        $date = $this->generateDateHeader();
        $authorizationHeader = $this->generateAuthorizationHeader('POST', $url, $date, $body);

        // Prepare the headers for the request
        $headers = [
            'Authorization' => $authorizationHeader,
            'Content-Type' => 'application/json',
            'Date' => $date,
            'x-ms-content-sha256' => $this->hashRequestBody($body),
        ];

        // Make the request to Azure Communication Services
        try {
            $response = $this->client->post($this->endpoint . $url, [
                'headers' => $headers,
                'body' => $body,
            ]);

            return json_decode($response->getBody()->getContents(), true);

        } catch (GuzzleException $e) {
            Log::error('Error sending email via ACS: ' . $e->getMessage());
            return ['error' => 'Failed to send email', 'message' => $e->getMessage()];
        }
    }

    // Function to sign the request and generate the authorization header

    protected function generateDateHeader(): string
    {
        return Carbon::now('UTC')->toRfc7231String();
    }

    // Helper function to hash the body content

    protected function generateAuthorizationHeader($method, $url, $date, $body): string
    {
        $host = parse_url($this->endpoint, PHP_URL_HOST);  // Extract host from the endpoint
        $hashedBody = $this->hashRequestBody($body);

        // Prepare the string to sign
        $stringToSign = $method . "\n" . $url . "\n" . $date . ";" . $host . ";" . $hashedBody;

        // Decode the access key (Base64 encoded) and use it to sign the string
        $key = base64_decode($this->accessKey);
        $signature = base64_encode(hash_hmac('sha256', $stringToSign, $key, true));

        return "HMAC-SHA256 SignedHeaders=date;host;x-ms-content-sha256&Signature=" . $signature;
    }


    // Function to send email using Azure Communication Services

    protected function hashRequestBody($body): string
    {
        return base64_encode(hash('sha256', $body, true));
    }

}

