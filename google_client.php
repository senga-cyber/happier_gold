<?php
require_once __DIR__ . '/vendor/autoload.php';

function getBaseUrl(): string
{
    $baseUrl = getenv('APP_URL');
    if ($baseUrl) {
        return rtrim($baseUrl, '/');
    }

    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
        $proto = explode(',', $_SERVER['HTTP_X_FORWARDED_PROTO'])[0];
        $scheme = trim($proto);
    } else {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    }

    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $basePath = '';
    if (!empty($_SERVER['SCRIPT_NAME'])) {
        $basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
        if ($basePath === '/' || $basePath === '.') {
            $basePath = '';
        }
    }

    return $scheme . '://' . $host . $basePath;
}

function getRedirectUri(): string
{
    $baseUrl = getBaseUrl();
    if (preg_match('~/oauth_callback\.php$~', $baseUrl)) {
        return $baseUrl;
    }
    return $baseUrl . '/oauth_callback.php';
}

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
