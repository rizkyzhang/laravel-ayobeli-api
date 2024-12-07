<?php

namespace App\Http\Controllers;

use App\Services\AzureEmailService;
use Exception;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    protected AzureEmailService $emailService;

    public function __construct(AzureEmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function sendEmail(Request $request)
    {
        $to = $request->input('to');
        $subject = $request->input('subject');
        $htmlContent = $request->input('content');

        try {
            $response = $this->emailService->sendEmail($to, $subject, $htmlContent);
            return response()->json(['message' => 'Email sent successfully', 'response' => $response], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to send email', 'error' => $e->getMessage()], 500);
        }
    }
}
