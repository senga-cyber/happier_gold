<?php
session_start();
require_once __DIR__ . '/google_client.php';

// 1) Base URL : priorité à APP_URL, sinon auto-détection
$baseUrl = getBaseUrl();

if (!$baseUrl) {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $baseUrl = $scheme . '://' . $host;
}

// 2) Client Google avec redirect EXACT
$client = buildGoogleClient(getRedirectUri());

if (!isset($_GET['code'])) {
    die('Code OAuth manquant');
}

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

if (isset($token['error'])) {
    die('Erreur OAuth : ' . ($token['error_description'] ?? $token['error']));
}

$_SESSION['access_token'] = $token;

// Redirige vers la logique métier
header('Location: create_event.php');
exit;
