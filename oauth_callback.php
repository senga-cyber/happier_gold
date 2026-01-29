<?php
session_start();
require_once __DIR__ . '/google_client.php';

$baseUrl = getenv('APP_URL');

// Client Google avec redirect FIXE
$client = buildGoogleClient($baseUrl . '/oauth_callback.php');

if (!isset($_GET['code'])) {
    die('Code OAuth manquant');
}

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

if (isset($token['error'])) {
    die('Erreur OAuth : ' . $token['error_description']);
}

$_SESSION['access_token'] = $token;

// Redirige vers la logique métier
header('Location: create_event.php');
exit;
