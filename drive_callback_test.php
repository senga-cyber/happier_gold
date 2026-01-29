<?php
session_start();
require_once __DIR__ . '/google_client.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/phpqrcode/qrlib.php';

// Détecte l'URL de base (local ou Render)
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$baseUrl = $scheme . '://' . $host;

$client = buildGoogleClient($baseUrl . '/drive_callback_test.php');

/**
 * 1) Récupérer le token
 */
if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (isset($token['error'])) {
        exit('Erreur OAuth: ' . htmlspecialchars($token['error_description'] ?? $token['error']));
    }

    $_SESSION['access_token'] = $token;
    $client->setAccessToken($token);

} elseif (isset($_SESSION['access_token'])) {
    $client->setAccessToken($_SESSION['access_token']);
} else {
    exit("Aucun code de retour et aucun token enregistré. Recommencez via create_event.php");
}

/**
 * 2) Créer le dossier Drive
 */
$event_name = $_SESSION['event_name'] ?? 'LucasPro Event';

$service = new Google_Service_Drive($client);

$fileMetadata = new Google_Service_Drive_DriveFile([
    'name' => $event_name,
    'mimeType' => 'application/vnd.google-apps.folder'
]);

$folder = $service->files->create($fileMetadata, ['fields' => 'id']);
$folderId = $folder->id;

/**
 * 3) Permission public (lecture)
 */
$permission = new Google_Service_Drive_Permission([
    'type' => 'anyone',
    'role' => 'reader'
]);
$service->permissions->create($folderId, $permission);

/**
 * 4) Lien notify public (PLUS de localhost)
 */
$clientEmail = $_SESSION['client_email'] ?? '';
$notifyUrl = $baseUrl . "/notify.php?folder=https://drive.google.com/drive/folders/$folderId"
    . "&event=" . urlencode($event_name)
    . "&client=" . urlencode($clientEmail);

/**
 * 5) QR en base64 (pas besoin d’écrire qr.png)
 */
ob_start();
QRcode::png($notifyUrl, null, QR_ECLEVEL_L, 6);
$qrImageData = base64_encode(ob_get_clean());
$qrSrc = "data:image/png;base64," . $qrImageData;

/**
 * 6) Affichage
 */
echo "<h2 style='color:gold; font-family:sans-serif;'>✅ Événement '" . htmlspecialchars($event_name) . "' créé avec succès.</h2>";
echo "<p><a href='" . htmlspecialchars($notifyUrl) . "' target='_blank'>📩 Ouvrir la page notify</a></p>";
echo "<p><img src='$qrSrc' alt='QR Code'></p>";
