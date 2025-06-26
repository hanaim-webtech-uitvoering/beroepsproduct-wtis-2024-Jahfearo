<?php
session_start();
require_once __DIR__ . '/../db_connectie.php';
$pdo = maakVerbinding();

// Helper voor foutmeldingen
function foutmelding(string $msg) {
  global $fout;
  $fout = $msg;
}

// Formulierverwerking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['cmdBestelling'] ?? '') === 'Bevestig') {
  $voornaam   = trim($_POST['voornaam'] ?? '');
  $achternaam = trim($_POST['achternaam'] ?? '');
  $adres      = trim($_POST['adres'] ?? '');
  $username   = $_SESSION['username'] ?? null;
  $winkelmandje = $_SESSION['winkelmandje'] ?? [];

  if (!$voornaam || !$achternaam || !$adres) {
    foutmelding("Vul alle vereiste velden in.");
  } elseif (empty($winkelmandje)) {
    foutmelding("Je winkelwagen is leeg.");
  } else {
    try {
      $pdo->beginTransaction();

      $volledigeNaam = $voornaam . ' ' . $achternaam;

      $stmt = $pdo->prepare("INSERT INTO Pizza_Order (client_username, client_name, personnel_username, datetime, status, address)
        VALUES (:username, :naam, :personeel, GETDATE(), 1, :adres)");

      $stmt->execute([
        ':username' => $username,
        ':naam'     => $volledigeNaam,
        ':personeel'=> 'wbos', // dummy gebruiker
        ':adres'    => $adres
      ]);

      $orderId = $pdo->lastInsertId();

      $stmtProd = $pdo->prepare("INSERT INTO Pizza_Order_Product (order_id, product_name, quantity)
        VALUES (:orderId, :product, :qty)");

      foreach ($winkelmandje as $naam => $qty) {
        $stmtProd->execute([
          ':orderId' => $orderId,
          ':product' => $naam,
          ':qty'     => $qty
        ]);
      }

      $pdo->commit();
      unset($_SESSION['winkelmandje']);
      $bestelnummer = $orderId;

    } catch (Exception $e) {
      $pdo->rollBack();
      foutmelding("Bestelling mislukt: " . $e->getMessage());
    }
  }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <link rel="stylesheet" href="normalize.css">
  <link rel="stylesheet" href="styles.css">
  <title>Pizzeria Sole Machina – Bestelbevestiging</title>
</head>
<body>
  <div class="wrapper">
    <div class="logo">
      <div class="grid-container img">
        <img src="img/Pizzeria_Sole_Machina_Logo.jpg" alt="logo van pizzeria sole machina">
      </div>
    </div>
    <nav>
      <ul>
        <li><a href="menu.php">menu</a></li>
        <li><a href="privacy.php">privacy verklaring</a></li>
        <li><a href="contact.php">contact</a></li>
      </ul>
    </nav>
    <div class="thuispagina"><a href="index.php">thuispagina</a></div>
    <div class="profiel"><a href="profiel.php">profiel</a></div>
    <div class="winkelwagen"><a href="winkelwagen.php">winkelwagen</a></div>
    <div class="reclamelinks">
      <strong>familie pakket:</strong> Bestel 3 pizza's en krijg 1 gratis drankje
      <div class="grid-container img">
        <img src="img/pizzamagerita.jpg" alt="pizza Margherita">
        <img src="img/Coca-Cola-Sleek-33cl-Blik-200x667.png" alt="blikje Coca-Cola">
      </div>
    </div>
    <div class="reclamerechts">
      <strong>pizza woensdag</strong> alle pizza's 20% korting op woensdag
      <div class="grid-container img">
        <img src="img/pizzasalami.jpg" alt="pizza salami">
      </div>
    </div>
    <div class="footer"></div>

    <div class="content">
      <h1>Bestelbevestiging</h1>

      <?php if (!empty($fout)): ?>
        <p style="color:red;"><strong><?= htmlspecialchars($fout) ?></strong></p>
        <p><a href="bestelling_formulier.php">Ga terug naar het formulier</a></p>
      <?php elseif (isset($bestelnummer)): ?>
        <p>Bedankt voor je bestelling!</p>
        <p>Je bestelling is geplaatst onder bestelnummer <strong>#<?= $bestelnummer ?></strong>.</p>
        <p><a href="menu.php">Terug naar het menu</a></p>
      <?php else: ?>
        <p>Er is iets misgegaan. Probeer het opnieuw.</p>
        <p><a href="menu.php">Ga terug naar het menu</a></p>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>