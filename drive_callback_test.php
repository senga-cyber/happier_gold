<?php
require_once 'vendor/autoload.php';
session_start();

$client = new Google_Client();
$client->setAuthConfig('credentials.json');
$client->addScope(Google_Service_Drive::DRIVE);
$client->setRedirectUri('http://localhost/lucaspro_qr_drive_final/drive_callback.php');

// Étape OBLIGATOIRE : vérifier si Google a renvoyé un code
if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (isset($token['error'])) {
        exit('Erreur lors de la récupération du token: ' . htmlspecialchars($token['error_description']));
    }

    $_SESSION['access_token'] = $token;
    $client->setAccessToken($token);
} elseif (isset($_SESSION['access_token'])) {
    $client->setAccessToken($_SESSION['access_token']);
} else {
    exit('Aucun code de retour et aucun token enregistré. Recommencez.');
}

// Récupérer le nom de l'événement
$event_name = $_SESSION['event_name'] ?? 'LucasPro Event';

// Créer le dossier sur Google Drive
$service = new Google_Service_Drive($client);

$fileMetadata = new Google_Service_Drive_DriveFile([
    'name' => $event_name,
    'mimeType' => 'application/vnd.google-apps.folder'
]);

$folder = $service->files->create($fileMetadata, ['fields' => 'id']);
$folderId = $folder->id;

// Rendre le dossier public
$permission = new Google_Service_Drive_Permission([
    'type' => 'anyone',
    'role' => 'reader'
]);
$service->permissions->create($folderId, $permission);

// Générer le QR Code
$link = "http://localhost/lucaspro_qr_drive_final/notify.php?folder=https://drive.google.com/drive/folders/$folderId&event=" . urlencode($event_name) . "&client=" . urlencode($_SESSION['client_email']);
require 'phpqrcode/qrlib.php';
QRcode::png($link, "qr.png");

// Affichage final
echo "<h2 style='color:gold; font-family:sans-serif;'>✅ Événement '$event_name' créé avec succès.</h2>";
echo "<p><a href='$link' target='_blank'>📁 Ouvrir le dossier Google Drive</a></p>";
echo "<p><img src='qr.png' alt='QR Code de téléchargement'></p>";

?>
