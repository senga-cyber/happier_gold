<?php
require_once 'vendor/autoload.php';
require_once 'phpqrcode/qrlib.php';

session_start();

$client = new Google_Client();
$client->setAuthConfig('credentials.json');
$client->addScope(Google_Service_Drive::DRIVE);
$client->setRedirectUri('http://localhost/lucaspro_qr_drive_final/create_event.php');
$client->setAccessType('offline');

// Étape 1 : Rediriger vers Google si aucun code
if (!isset($_GET['code'])) {
    $_SESSION['event_name'] = $_POST['event_name'] ?? 'LucasPro Event';
    $authUrl = $client->createAuthUrl();
    header('Location: ' . $authUrl);
    exit;
}

// Étape 2 : On reçoit le code de Google
$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

if (isset($token['error'])) {
    exit('Erreur OAuth : ' . htmlspecialchars($token['error_description']));
}

$client->setAccessToken($token);
$_SESSION['access_token'] = $token;

// Créer le dossier Drive
$service = new Google_Service_Drive($client);
$event_name = $_SESSION['event_name'] ?? 'LucasPro Event';

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

// Générer un lien unique pour ce dossier
$link = "https://drive.google.com/drive/folders/$folderId";

// Nom unique pour le QR code
$qrFilename = "qr_" . $folderId . ".png";
QRcode::png($link, $qrFilename);

// Affichage HTML
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Événement créé</title>
    <style>
        body { font-family: Arial, sans-serif; background: #111; color: gold; text-align: center; }
        a { color: #00aaff; text-decoration: none; }
        .share-buttons img { width: 50px; margin: 10px; cursor: pointer; }
        .qr-container img { width: 200px; border: 5px solid gold; border-radius: 10px; }
    </style>
</head>
<body>
    <h2>✅ Événement "<?php echo htmlspecialchars($event_name); ?>" créé avec succès.</h2>
    <p><a href="<?php echo $link; ?>" target="_blank">📁 Accéder au dossier Google Drive</a></p>

    <!-- QR Code cliquable -->
    <div class="qr-container">
        <a href="<?php echo $link; ?>" target="_blank">
            <img src="<?php echo $qrFilename; ?>" alt="QR Code de téléchargement">
        </a>
    </div>

    <p><a href="<?php echo $qrFilename; ?>" download>Télécharger le QR Code</a></p>

    <h3>📢 Partager ce lien :</h3>
    <div class="share-buttons">
        <!-- WhatsApp -->
        <a href="https://api.whatsapp.com/send?text=<?php echo urlencode($link); ?>" target="_blank">
            <img src="https://cdn-icons-png.flaticon.com/512/733/733585.png" alt="WhatsApp">
        </a>
        <!-- Facebook -->
        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($link); ?>" target="_blank">
            <img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" alt="Facebook">
        </a>
        <!-- Twitter -->
        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($link); ?>&text=<?php echo urlencode('Découvrez mon dossier Google Drive'); ?>" target="_blank">
            <img src="https://cdn-icons-png.flaticon.com/512/733/733579.png" alt="Twitter">
        </a>
        <!-- Email -->
        <a href="mailto:?subject=<?php echo urlencode('Lien de mon dossier Google Drive'); ?>&body=<?php echo urlencode($link); ?>">
            <img src="https://cdn-icons-png.flaticon.com/512/732/732200.png" alt="Email">
        </a>
    </div>
</body>
</html>
