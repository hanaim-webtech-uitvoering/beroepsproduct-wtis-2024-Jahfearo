<?php
session_start();
require_once __DIR__ . '/../db_connectie.php';
$pdo = maakVerbinding();

// Hulpfunctie om prijzen netjes te formatteren
function formatPrijs(float $bedrag): string {
    return '€ ' . number_format($bedrag, 2, ',', '.');
}

// 1) Verwerk acties

// a) Toevoegen via menu.php
if (isset($_POST['cmdWinkelmandje']) && $_POST['cmdWinkelmandje'] === 'Add') {
    $naam = $_POST['productnaam'];
    $qty  = max(1, (int)$_POST['quantity']);

    $_SESSION['winkelmandje'][$naam] =
        ($_SESSION['winkelmandje'][$naam] ?? 0) + $qty;

    header('Location: winkelwagen.php');
    exit;
}

// b) Verwijderen vanuit winkelwagen
if (isset($_POST['remove']) && isset($_SESSION['winkelmandje'][$_POST['remove']])) {
    unset($_SESSION['winkelmandje'][$_POST['remove']]);
    header('Location: winkelwagen.php');
    exit;
}

// 2) Weergave winkelwagen
$viewWinkelmand = '';

if (!empty($_SESSION['winkelmandje'])) {
    $rows = '';
    $subtotaal = 0.0;

    foreach ($_SESSION['winkelmandje'] as $prodNaam => $aantal) {
        $stmt = $pdo->prepare("SELECT price FROM Product WHERE [name] = :naam");
        $stmt->execute([':naam' => $prodNaam]);
        $prijs = (float)($stmt->fetchColumn() ?: 0);

        $regelSubtotaal = $prijs * $aantal;
        $subtotaal += $regelSubtotaal;

        $rows .= "
        <tr>
            <td>" . htmlspecialchars($prodNaam) . "</td>
            <td>$aantal</td>
            <td>" . formatPrijs($prijs) . "</td>
            <td>" . formatPrijs($regelSubtotaal) . "</td>
            <td>
              <form method=\"post\">
                <button name=\"remove\" value=\"" . htmlspecialchars($prodNaam) . "\">Verwijder</button>
              </form>
            </td>
        </tr>";
    }

    $btw = $subtotaal * 0.09;
    $totaal = $subtotaal + $btw;

    $viewWinkelmand = "
    <table>
      <tr>
        <th>Product</th>
        <th>Aantal</th>
        <th>Prijs p/st</th>
        <th>Subtotaal</th>
        <th>Actie</th>
      </tr>
      $rows
      <tr><td colspan=\"5\" style=\"background:#D32F2F;\"></td></tr>
      <tr>
        <td colspan=\"4\" style=\"text-align:right\">Subtotaal:</td>
        <td>" . formatPrijs($subtotaal) . "</td>
      </tr>
      <tr>
        <td colspan=\"4\" style=\"text-align:right\">BTW (9%):</td>
        <td>" . formatPrijs($btw) . "</td>
      </tr>
      <tr>
        <td colspan=\"4\" style=\"text-align:right;font-weight:700;\">Totaal:</td>
        <td style=\"background:#D32F2F;font-weight:700;\">" . formatPrijs($totaal) . "</td>
      </tr>
    </table>";
} else {
    $viewWinkelmand = '<p>De winkelmand is leeg.</p>';
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="normalize.css">
  <link rel="stylesheet" href="styles.css">
  <title>Pizzeria Sole Machina - Winkelwagen</title>
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
      <h1>Uw winkelwagen</h1>
      <div class="grid-container">
        <?= $viewWinkelmand ?>
      </div>
    </div>
  </div>
</body>
</html>