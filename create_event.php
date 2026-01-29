<?php
session_start();
require_once __DIR__ . '/google_client.php';
require_once __DIR__ . '/vendor/autoload.php';

// Vérifier si l'utilisateur est authentifié
if (!isset($_SESSION['access_token'])) {
    header('Location: index.php');
    exit;
}

$baseUrl = getenv('APP_URL');
if (!$baseUrl) {
    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
        $scheme = $_SERVER['HTTP_X_FORWARDED_PROTO'];
    } else {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    }
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $baseUrl = $scheme . '://' . $host;
}
$baseUrl = rtrim($baseUrl, '/');

// Client avec token existant
$client = buildGoogleClient($baseUrl . '/oauth_callback.php');
$client->setAccessToken($_SESSION['access_token']);

// Traitement du formulaire pour créer l'événement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['event_name'] = $_POST['event_name'] ?? 'LucasPro Event';
    // Créer le dossier Drive ici
    // (logique métier)
    exit;
}
