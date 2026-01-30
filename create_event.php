<?php
session_start();
require_once __DIR__ . '/google_client.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/phpqrcode/qrlib.php';

// Vérifier que l'utilisateur est authentifié
if (!isset($_SESSION['access_token'])) {
    header('Location: index.php');
    exit;
}

// Détecte l'URL de base (local ou Render)
$baseUrl = getBaseUrl();
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

// Initialiser le client Google avec le token existant
$client = buildGoogleClient(getRedirectUri());
$client->setAccessToken($_SESSION['access_token']);

// Traiter la création du dossier Drive
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['event_name'] = $_POST['event_name'] ?? 'LucasPro Event';
    
    // Créer le dossier Drive
    $service = new Google_Service_Drive($client);
    $fileMetadata = new Google_Service_Drive_DriveFile([
        'name' => $_SESSION['event_name'],
        'mimeType' => 'application/vnd.google-apps.folder'
    ]);
    
    $folder = $service->files->create($fileMetadata, ['fields' => 'id']);
    $folderId = $folder->id;
    
    // Donner accès public (lecture)
    $permission = new Google_Service_Drive_Permission([
        'type' => 'anyone',
        'role' => 'reader'
    ]);
    $service->permissions->create($folderId, $permission);
    
    // Générer l'URL de notification avec QR
    $notifyUrl = $baseUrl . "/notify.php?folder=https://drive.google.com/drive/folders/$folderId"
        . "&event=" . urlencode($_SESSION['event_name']);
    
    // Générer le QR code en base64
    ob_start();
    QRcode::png($notifyUrl, null, QR_ECLEVEL_L, 6);
    $qrImageData = base64_encode(ob_get_clean());
    $qrSrc = "data:image/png;base64," . $qrImageData;
    
    // Afficher le résultat
    echo "<h2 style='color:gold; font-family:sans-serif;'>✅ Dossier créé : " . htmlspecialchars($_SESSION['event_name']) . "</h2>";
    echo "<img src='$qrSrc' alt='QR Code'>";
    exit;
}
