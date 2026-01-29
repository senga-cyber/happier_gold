<?php
// notify.php

// Sécurisation des paramètres
$eventName   = isset($_GET['event']) ? htmlspecialchars($_GET['event']) : 'Événement inconnu';
$clientEmail = isset($_GET['client']) ? htmlspecialchars($_GET['client']) : 'client inconnu';
$folderLink  = $_GET['folder'] ?? 'https://drive.google.com/';

// Email de destination (le tien)
$to = 'lucasmpala2@gmail.com';

// Sujet et message
$subject = "📸 Dossier accédé - $eventName";

$message = "Bonjour,\n\n"
    . "Le client ($clientEmail) vient d'accéder à son dossier photo.\n\n"
    . "Événement : $eventName\n"
    . "Date : " . date('Y-m-d H:i:s') . "\n\n"
    . "LucasPro QR Drive";

// Détection du domaine (Render ou local)
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host   = $_SERVER['HTTP_HOST'] ?? 'localhost';

// From valide (IMPORTANT)
$from = "LucasPro QR Drive <no-reply@$host>";

// Headers propres
$headers  = "From: $from\r\n";
$headers .= "Reply-To: no-reply@$host\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Tentative d'envoi email (silencieuse si échec)
@mail($to, $subject, $message, $headers);

// Redirection vers le dossier Google Drive
header("Location: $folderLink");
exit;
