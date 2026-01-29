<?php
require_once __DIR__ . '/vendor/autoload.php';

function buildGoogleClient(string $redirectUri): Google_Client
{
    $credsJson = getenv('GOOGLE_CREDENTIALS_JSON');

    if (!$credsJson) {
        // DEV local : fichier credentials.json
        $localPath = __DIR__ . '/credentials.json';
        if (!file_exists($localPath)) {
            throw new Exception('credentials.json introuvable en local et GOOGLE_CREDENTIALS_JSON non défini.');
        }
        $credsPath = $localPath;
    } else {
        // PROD Render : écrire un fichier temporaire
        $tmpPath = sys_get_temp_dir() . '/google_credentials.json';
        file_put_contents($tmpPath, $credsJson);
        $credsPath = $tmpPath;
    }

    $client = new Google_Client();
    $client->setAuthConfig($credsPath);

    // IMPORTANT : le scope (Drive)
    $client->addScope(Google_Service_Drive::DRIVE);

    // IMPORTANT : redirect URI EXACT
    $client->setRedirectUri($redirectUri);

    $client->setAccessType('offline');
    $client->setPrompt('consent'); // force refresh_token (utile en prod)

    return $client;
}
