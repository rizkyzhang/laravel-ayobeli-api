<?php

namespace App\Http\Controllers;

use http\Env;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json($user, 201);
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = User::where('email', $request->email)->first();
            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json(['token' => $token], 200);
        }

        return response()->json(['error' => 'Invalid credentials'], 401);
    }
//
//    public function signRequest(Request $request)
//    {
//        $message = $request->header('X-Message');
//
//        $serverPublicKey = base64_decode(getenv('SERVER_PUBLIC_KEY'));
//        $clientPrivateKey = base64_decode(getenv('CLIENT_PRIVATE_KEY'));
//
//        $sharedSecret = sodium_crypto_scalarmult($clientPrivateKey, $serverPublicKey);
//        dd(base64_encode($sharedSecret));
//        $signature = hash_hmac('sha256', $message, $sharedSecret);
//
//        return response()->json(['data' => $signature]);
//    }
//
//    public function verifyRequest(Request $request)
//    {
//        $clientSignature = $request->header('X-Signature');
//        $timestamp = $request->header('X-Timestamp');
//        $method = $request->method();
//        $url = $request->path();
//        $body = json_encode($request->all());
//
//        // Combine timestamp, method, URL, and body
//        $message = $timestamp . $method . $url . $body;
//
//        $clientPublicKey = base64_decode(getenv('CLIENT_PUBLIC_KEY'));
//        $serverPrivateKey = base64_decode(getenv('SERVER_PRIVATE_KEY'));
//
//        $sharedSecret = sodium_crypto_scalarmult($serverPrivateKey, $clientPublicKey);
//        $serverSignature = hash_hmac('sha256', $message, $sharedSecret);
//
//        dd(base64_encode($sharedSecret));
//
//        if ($clientSignature !== $serverSignature) {
//            return response()->json(['error' => 'Invalid signature', 'serverSignature' => $serverSignature, 'clientSignature' => $clientSignature,
//                'message' => $message], 401);
//        }
//
//        return response()->json(['message' => 'Signature verified'], 200);
//    }
}
