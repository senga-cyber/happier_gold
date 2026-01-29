<?php
require_once __DIR__ . '/vendor/autoload.php';
session_start();

/**
 * Détecte l'URL de base (local ou Render)
 */
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$baseUrl = $scheme . '://' . $host;

/**
 * Crée un client Google en utilisant :
 * - Render: GOOGLE_CREDENTIALS_JSON
 * - Local : credentials.json
 */
$client = new Google_Client();

$credsJson = getenv('GOOGLE_CREDENTIALS_JSON');
if ($credsJson) {
    // Render
    $client->setAuthConfig(json_decode($credsJson, true));
} else {
    // Local
    $client->setAuthConfig(__DIR__ . '/credentials.json');
}

$client->addScope(Google_Service_Drive::DRIVE);
$client->setAccessType('offline');
$client->setPrompt('consent');

/**
 * On veut que le retour Google aille au callback
 */
$client->setRedirectUri($baseUrl . '/drive_callback_test.php');

/**
 * Si on arrive ici via POST (form), on stocke le nom
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['event_name'] = $_POST['event_name'] ?? 'LucasPro Event';
}

/**
 * Démarre l'auth OAuth
 */
$authUrl = $client->createAuthUrl();
header('Location: ' . $authUrl);
exit;
