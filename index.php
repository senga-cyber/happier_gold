<?php
session_start();

// Autoload + Google client helper
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/google_client.php';

/**
 * Déterminer l'URL de base
 * - En prod (Render) : APP_URL
 * - En local : détection automatique
 */
$baseUrl = getenv('APP_URL');

if (!$baseUrl) {
    // Cas Render derrière proxy HTTPS
    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
        $scheme = $_SERVER['HTTP_X_FORWARDED_PROTO'];
    } else {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    }

    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $baseUrl = $scheme . '://' . $host;
}

// Nettoyage
$baseUrl = rtrim($baseUrl, '/');

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['event_name'] = $_POST['event_name'] ?? 'LucasPro Event';

    // OAuth Google
    $client = buildGoogleClient($baseUrl . '/create_event.php');
    error_log("REDIRECT_URI = " . $baseUrl . "/create_event.php");
    $authUrl = $client->createAuthUrl();

    header('Location: ' . $authUrl);
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Lucas Pro - Événements</title>

  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #111;
      color: #ffd700;
    }
    header {
      background: rgba(0, 0, 0, 0.7);
      padding: 1rem;
      text-align: center;
      font-size: 2rem;
      font-weight: bold;
    }
    .diapo {
      position: fixed;
      top: 0;
      left: 0;
      z-index: -1;
      width: 100%;
      height: 100%;
      background-size: cover;
      background-position: center;
      animation: diapo 50s infinite alternate;
    }
    @keyframes diapo {
      0%   { background-image: url('assets/1.PNG'); }
      10%  { background-image: url('assets/2.jpg'); }
      20%  { background-image: url('assets/3.jpeg'); }
      30%  { background-image: url('assets/4.jpg'); }
      40%  { background-image: url('assets/5.jpg'); }
      50%  { background-image: url('assets/6.jpg'); }
      60%  { background-image: url('assets/7.jpg'); }
      70%  { background-image: url('assets/8.jpg'); }
      80%  { background-image: url('assets/9.jpg'); }
      90%  { background-image: url('assets/10.jpg'); }
      100% { background-image: url('assets/11.jpg'); }
    }
    form {
      background: rgba(0, 0, 0, 0.8);
      padding: 2rem;
      max-width: 500px;
      margin: 5rem auto;
      border-radius: 8px;
      box-shadow: 0 0 20px #ffd70088;
    }
    label, input {
      display: block;
      width: 100%;
      margin-bottom: 1rem;
      font-size: 1.1rem;
    }
    input[type="submit"] {
      background: #ffd700;
      color: #111;
      font-weight: bold;
      border: none;
      padding: 1rem;
      cursor: pointer;
    }
    footer {
      text-align: center;
      padding: 2rem;
      font-size: 0.9rem;
      background: #000;
      color: #fff;
    }
    h1 {
      text-align: center;
      padding: 1.5rem;
      font-size: 0.95rem;
      background: #000;
      color: #867409;
    }
  </style>
</head>

<body>
  <div class="diapo"></div>

  <header>
    <img src="assets/logo.png" alt="Logo" height="60" /><br />
    Happier Gold<br>
    Lucas
  </header>

  <form method="POST">
    <label for="event_name">Nom de l'événement :</label>
    <input type="text" id="event_name" name="event_name" required />
    <input type="submit" value="Créer l'événement et générer un QR" />
  </form>

  <h1>
    Contact : lucasmpala2@gmail.com · WhatsApp : +243978255830 · Pinterest : @lucaspro
  </h1>

  <footer>
    © chadowork 2024 - Tous droits réservés.
  </footer>
</body>
</html>
