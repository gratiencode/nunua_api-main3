<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use App\Services\GoogleAuthService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class GoogleAuthController extends Controller
{
    // Redirect user to Google login page
    public function redirectToGoogle()
    {
        $client = GoogleAuthService::getClient();
        $authUrl = $client->createAuthUrl();

        return redirect($authUrl);
    }

    // Handle Google's callback
    public function handleGoogleCallback(Request $request)
    {
        $code = $request->query('code');

        if (!$code) {
            return response()->json(['error' => 'Authorization code is required.'], 400);
        }

        $client = GoogleAuthService::getClient();

        try {
            // Exchange the authorization code for tokens
            $token = $client->fetchAccessTokenWithAuthCode($code);
        } catch (\Exception $e) {
            return response()->json(['error' => "Failed to fetch token: {$e->getMessage()}"], 400);
        }

        // Get the ID token
        $idToken = $token['id_token'] ?? null;

        if (!$idToken) {
            return response()->json(['error' => 'ID Token not found.'], 400);
        }

        // Decode ID token WITHOUT signature verification (same as Django)
        list($header, $payload, $signature) = explode('.', $idToken);

        $payloadJson = base64_decode(str_replace(['-', '_'], ['+', '/'], $payload));
        $userInfo = json_decode($payloadJson, true);

        $email = $userInfo['email'] ?? null;

        if (!$email) {
            return response()->json(['error' => 'Email not found in token.'], 400);
        }

        // Check user exists
        $user = User::where('email', $email)
            ->first();


        // Create user if not exists
        if (!$user) {
            $user = User::create([
                'email' => $email,
                'pswd' => null,
            ]);
        }

        // Log user in
        Auth::login($user);

       
        $token = $user->createToken('google-auth')->plainTextToken;

        $agent = new Agent();

        if ($agent->isMobile()) {
            return redirect()->to(
                'nunua://auth/callback?token=' . urlencode($token)
            );
        }

        return redirect()->to(
            'https://nunua.markets?token=' . urlencode($token)
        );
    }
}
