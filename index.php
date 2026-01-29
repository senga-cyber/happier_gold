<?php
session_start();
require_once __DIR__ . '/google_client.php';
require_once __DIR__ . '/vendor/autoload.php';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['event_name'] = $_POST['event_name'] ?? 'LucasPro Event';

    // Détecte l'URL de base (local ou Render)
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $baseUrl = $scheme . '://' . $host;

    $client = buildGoogleClient($baseUrl . '/create_event.php');
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
      top: 0; left: 0;
      z-index: -1;
      width: 100%;
      height: 100%;
      background-size: cover;
      background-position: center;
      animation: diapo 50s infinite alternate;
    }
    @keyframes diapo {
      0%   { background-image: url('assets/1.PNG'); }
      33%  { background-image: url('assets/2.jpg'); }
      20%  { background-image: url('assets/3.jpeg'); }
      50%  { background-image: url('assets/4.jpg'); }
      80%  { background-image: url('assets/5.jpg'); }
      0%   { background-image: url('assets/6.jpg'); }
      33%  { background-image: url('assets/7.jpg'); }
      20%  { background-image: url('assets/8.jpg'); }
      50%  { background-image: url('assets/9.jpg'); }
      80%  { background-image: url('assets/10.jpg'); }
      0%   { background-image: url('assets/11.jpg'); }
      33%  { background-image: url('assets/12.jpg'); }
      20%  { background-image: url('assets/13.jpg'); }
      50%  { background-image: url('assets/14.jpg'); }
      80%  { background-image: url('assets/15.jpg'); }
      0%   { background-image: url('assets/16.jpg'); }
      33%  { background-image: url('assets/17.jpg'); }
      20%  { background-image: url('assets/18.jpg'); }
      50%  { background-image: url('assets/19.jpg'); }
      80%  { background-image: url('assets/20.jpg'); }
      0%   { background-image: url('assets/21.jpg'); }
      33%  { background-image: url('assets/22.jpg'); }
      20%  { background-image: url('assets/23.jpg'); }
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
      font-size: 1.2rem;
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
      font-size: 1rem;
      background: #000;
      color: #fff;
    }
    h1 {
      text-align: center;
      padding: 2rem;
      font-size: 1rem;
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
    @copyright chadowork 2024 - Tous droits réservés.
  </footer>
</body>
</html>
