<?php

session_start();


if (!isset($_SESSION['username']) || ($_SESSION['role'] ?? '') !== 'Personnel') {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../db_connectie.php';
$pdo = maakVerbinding();

$stmt = $pdo->query("
    SELECT 
      o.order_id,
      o.client_username,
      o.client_name,
      o.address,
      o.datetime,
      o.status
    FROM Pizza_Order o
    WHERE o.status IN (1,2)
    ORDER BY o.datetime ASC
");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);


foreach ($orders as &$ord) {
    $stmtItems = $pdo->prepare("
        SELECT product_name, quantity
        FROM Pizza_Order_Product
        WHERE order_id = :order_id
    ");
    $stmtItems->execute([':order_id' => $ord['order_id']]);
    $ord['items'] = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
}
unset($ord);

function formatDatum(string $dt): string {
    return date('d-m-Y H:i', strtotime($dt));
}

function statusTekst(int $s): string {
    return match($s) {
        1 => 'In behandeling',
        2 => 'Onderweg',
        3 => 'Bezorgd',
        default => 'Onbekend',
    };
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="normalize.css">
  <link rel="stylesheet" href="styles.css">
  <title>Actieve Bestellingen – Pizzeria Sole Machina</title>
</head>
<body>
  <div class="wrapper">
    
    <div class="logo">
      <div class="grid-container img">
        <img src="img/Pizzeria_Sole_Machina_Logo.jpg" alt="logo van pizzeria">
      </div>
    </div>

    <nav>
      <ul>
        <li><a href="menu.php">menu</a></li>
        <li><a href="privacy.php">privacy verklaring</a></li>
        <li><a href="contact.php">contact</a></li>
        <li><a href="profiel.php">profiel</a></li>
        <li><a href="bestellingoverzichtPersoneel.php">Actieve bestellingen</a></li>
      </ul>
    </nav>

    
    <div class="thuispagina"><a href="index.php">thuispagina</a></div>
    <div class="profiel"><a href="profiel.php">profiel</a></div>
    <div class="winkelwagen"><a href="winkelwagen.php">winkelwagen</a></div>

   
    <div class="reclamelinks">
      <strong>familie pakket:</strong> Bestel 3 pizza's en krijg 1 gratis drankje
      <div class="grid-container img">
        <img src="img/pizzamagerita.jpg" alt="pizza">
        <img src="img/Coca-Cola-Sleek-33cl-Blik-200x667.png" alt="cola">
      </div>
    </div>
    <div class="reclamerechts">
      <strong>pizza woensdag</strong> alle pizza's 20% korting op woensdag
      <div class="grid-container img">
        <img src="img/pizzasalami.jpg" alt="salami">
      </div>
    </div>
    <div class="footer"></div>

    
    <div class="content">
      <h1>Detailoverzicht Bestellingen</h1>
      <?php if (empty($orders)): ?>
        <p>Er zijn momenteel geen actieve bestellingen.</p>
      <?php else: ?>
        <?php foreach ($orders as $ord): ?>
          <article class="bestelling">
            <h2>Bestelling #<?= htmlspecialchars($ord['order_id']) ?> – <?= formatDatum($ord['datetime']) ?></h2>
            <p><strong>Klant:</strong> 
              <?= htmlspecialchars($ord['client_name']) ?>
              <?php if (!empty($ord['client_username'])): ?>
                (<?= htmlspecialchars($ord['client_username']) ?>)
              <?php endif; ?>
            </p>
            <p><strong>Adres:</strong> 
              <?= !empty($ord['address']) ? htmlspecialchars($ord['address']) : 'nog geen adres' ?>
            </p>
            <p><strong>Status:</strong> <?= statusTekst((int)$ord['status']) ?></p>
            <h3>Producten</h3>
            <ul>
              <?php foreach ($ord['items'] as $item): ?>
                <li>
                  <?= htmlspecialchars($item['product_name']) ?> – <?= (int)$item['quantity'] ?>×
                </li>
              <?php endforeach; ?>
            </ul>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>