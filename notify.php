<?php
// notify.php

$eventName = $_GET['event'] ?? 'Événement inconnu';
$clientEmail = $_GET['client'] ?? 'client inconnu';

$to = 'lucasmpala2@gmail.com'; // <-- remplace par ton email réel
$subject = "📸 Dossier accédé - $eventName";
$message = "Le client ($clientEmail) vient d'accéder à son dossier photo pour l'événement : $eventName.";

$headers = "From: lucaspro@localhost\r\n";

mail($to, $subject, $message, $headers);

// Redirige vers le dossier Drive
$folderLink = $_GET['folder'] ?? 'https://drive.google.com/';
header("Location: $folderLink");
exit;
