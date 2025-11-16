<?php

namespace App\Services;

use Google_Client;

class GoogleAuthService
{
    public static function getClient()
    {
        $client = new Google_Client();

        // Load Google credentials from secret file
        $secretFile = base_path(config('services.google.secret_file'));
        $client->setAuthConfig($secretFile);

        // Set redirect URI
        $client->setRedirectUri(config('services.google.redirect_uri'));

        // Scopes
        $client->addScope('email');
        $client->addScope('profile');

        // Same behavior as Django
        $client->setAccessType('offline');
        $client->setIncludeGrantedScopes(true);

        return $client;
    }
}
