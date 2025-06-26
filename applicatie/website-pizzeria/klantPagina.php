<?php
//profielpagina
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../db_connectie.php';
$pdo = maakVerbinding();


if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit;
}


$username = $_SESSION['username'];
$role     = $_SESSION['role'] ?? 'Client';


$stmtUser = $pdo->prepare("
    SELECT first_name, last_name, address
    FROM users
    WHERE username = :username
");
$stmtUser->execute([':username' => $username]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);


$stmtOrders = $pdo->prepare("
    SELECT order_id, datetime, status, address
    FROM Pizza_Order
    WHERE client_username = :username
    ORDER BY datetime DESC
");
$stmtOrders->execute([':username' => $username]);
$orders = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);

foreach ($orders as &$order) {
    $stmtItems = $pdo->prepare("
        SELECT product_name, quantity
        FROM Pizza_Order_Product
        WHERE order_id = :order_id
    ");
    $stmtItems->execute([':order_id' => $order['order_id']]);
    $order['items'] = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
}
unset($order);

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
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="normalize.css">
  <link rel="stylesheet" href="styles.css">
  <title>Profiel – Pizzeria Sole Machina</title>
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

    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Personnel'): ?>
      <li><a href="bestellingoverzichtPersoneel.php">Actieve bestellingen</a></li>
      <li><a href="detailoverzichtBestelling.php">Detailoverzicht Bestelling</a></li>
    <?php endif; ?>
  </ul>
</nav>


    
    <div class="thuispagina"><a href="index.php">thuispagina</a></div>
    <div class="profiel"><a href="profiel.php">profiel</a></div>
    <div class="winkelwagen"><a href="winkelwagen.php">winkelwagen</a></div>

    
    <div class="reclamelinks">
      <strong>familie pakket:</strong> Bestel 3 pizza's en krijg 1 gratis drankje
      <div class="grid-container img">
        <img src="img/pizzamagerita.jpg" alt="pizza Margherita">
        <img src="img/Coca-Cola-Sleek-33cl-Blik-200x667.png" alt="Coca-Cola">
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
      <h1>Uw Profiel</h1>

      <section class="profiel-info">
        <h2>Persoonsgegevens</h2>
        <p><strong>Gebruikersnaam:</strong> <?= htmlspecialchars($username) ?></p>
        <p><strong>Naam:</strong> <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></p>
        <p><strong>Adres:</strong>
          <?= !empty($user['address']) ? htmlspecialchars($user['address']) : 'Nog geen adres' ?>
        </p>
        <form method="POST" action="" style="margin-top:1em;">
          <button type="submit" name="logout">Uitloggen</button>
        </form>
      </section>

      <section class="bestelgeschiedenis">
        <h2>Bestelgeschiedenis</h2>
        <?php if (empty($orders)): ?>
          <p>U heeft nog geen bestellingen geplaatst.</p>
        <?php else: ?>
          <?php foreach ($orders as $ord): ?>
            <article class="bestelling">
              <h3>Bestelling #<?= $ord['order_id'] ?> – <?= formatDatum($ord['datetime']) ?></h3>
              <p><strong>Status:</strong> <?= statusTekst((int)$ord['status']) ?></p>
              <p><strong>Bezorgadres:</strong> <?= htmlspecialchars($ord['address']) ?></p>
              <p><strong>Producten:</strong></p>
              <ul>
                <?php foreach ($ord['items'] as $item): ?>
                  <li><?= htmlspecialchars($item['product_name']) ?> – <?= (int)$item['quantity'] ?>×</li>
                <?php endforeach; ?>
              </ul>
            </article>
          <?php endforeach; ?>
        <?php endif; ?>
      </section>
    </div>
  </div>
</body>
</html>
