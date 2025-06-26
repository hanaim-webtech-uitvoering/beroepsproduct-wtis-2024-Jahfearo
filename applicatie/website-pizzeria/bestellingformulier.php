<?php
// bestelling_formulier.php
session_start();
require_once __DIR__ . '/../db_connectie.php';
$pdo = maakVerbinding();

// Helper om prijzen netjes te formatteren
function formatPrijs(float $bedrag): string {
    return '€ ' . number_format($bedrag, 2, ',', '.');
}

// Haal winkelwagen op
$cart = $_SESSION['winkelmandje'] ?? [];

// Bereken totalen en bouw de tabelraden
$subtotaal = 0.0;
$itemsHtml = '';
foreach ($cart as $naam => $qty) {
    $stmt = $pdo->prepare("SELECT price FROM Product WHERE [name] = :naam");
    $stmt->execute([':naam' => $naam]);
    $prijs = (float)$stmt->fetchColumn();
    $regelSub = $prijs * $qty;
    $subtotaal += $regelSub;
    $itemsHtml .= "
      <tr>
        <td>" . htmlspecialchars($naam) . "</td>
        <td>$qty</td>
        <td>" . formatPrijs($prijs) . "</td>
        <td>" . formatPrijs($regelSub) . "</td>
      </tr>";
}

$btw    = $subtotaal * 0.09;
$totaal = $subtotaal + $btw;

// Kijk of er een ingelogde gebruiker is
$user = $_SESSION['username'] ?? '';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <link rel="stylesheet" href="normalize.css">
  <link rel="stylesheet" href="styles.css">
  <title>Pizzeria Sole Machina – Bestelling bevestigen</title>
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
      <h1>Bestelling bevestigen</h1>

      <?php if (empty($cart)): ?>
        <p><em>Je winkelwagen is leeg. <a href="menu.php">Ga terug naar het menu</a>.</em></p>
      <?php else: ?>
        <h2>Inhoud winkelwagen</h2>
        <table>
          <tr>
            <th>Product</th><th>Aantal</th><th>Prijs p/st</th><th>Subtotaal</th>
          </tr>
          <?= $itemsHtml ?>
          <tr><td colspan="4" style="background:#D32F2F;"></td></tr>
          <tr>
            <td colspan="3" style="text-align:right">Subtotaal:</td>
            <td><?= formatPrijs($subtotaal) ?></td>
          </tr>
          <tr>
            <td colspan="3" style="text-align:right">BTW (9%):</td>
            <td><?= formatPrijs($btw) ?></td>
          </tr>
          <tr>
            <td colspan="3" style="text-align:right;font-weight:700">Totaal:</td>
            <td style="background:#D32F2F;font-weight:700"><?= formatPrijs($totaal) ?></td>
          </tr>
        </table>

        <h2>Gegevens invullen</h2>
        <form action="verwerkbestelling.php" method="post">
          <?php if ($user): ?>
            <div class="form-group">
              <label>
                Gebruikersnaam:
                <input type="text" name="username"
                       value="<?= htmlspecialchars($user) ?>" readonly>
              </label>
            </div>
          <?php else: ?>
            <!-- geen username-veld voor niet-ingelogden -->
          <?php endif; ?>

          <div class="form-group">
            <label>
              Voornaam:
              <input type="text" name="voornaam" required>
            </label>
          </div>
          <div class="form-group">
            <label>
              Achternaam:
              <input type="text" name="achternaam" required>
            </label>
          </div>
          <div class="form-group">
            <label>
              Adres, Postcode, Plaats:
              <input type="text" name="adres" 
                     placeholder="Straat 1, 1234AB, Stad" required>
            </label>
          </div>
          <p>
            <button type="submit" name="cmdBestelling" value="Bevestig">Bevestig bestelling</button>
          </p>
        </form>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>