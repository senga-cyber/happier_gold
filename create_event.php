<?php
session_start();
require_once __DIR__ . '/google_client.php';
require_once __DIR__ . '/vendor/autoload.php';

// Détecte l'URL de base (local ou Render)
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$baseUrl = $scheme . '://' . $host;

$client = buildGoogleClient($baseUrl . '/create_event.php');

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['event_name'] = $_POST['event_name'] ?? 'LucasPro Event';
    $authUrl = $client->createAuthUrl();
    header('Location: ' . $authUrl);
    exit;
}

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
