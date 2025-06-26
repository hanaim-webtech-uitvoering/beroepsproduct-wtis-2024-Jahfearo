<?php
session_start();
if (!isset($_SESSION['username']) || ($_SESSION['role'] ?? '') !== 'Personnel') {
    header('Location: index.php');
    exit;
}
require_once __DIR__ . '/../db_connectie.php';
$pdo = maakVerbinding();


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'], $_POST['order_id'], $_POST['status'])) {
    $orderId = (int) $_POST['order_id'];
    $status  = (int) $_POST['status'];
    
    if (in_array($status, [1, 2, 3], true)) {
        $upd = $pdo->prepare("UPDATE Pizza_Order SET status = :status WHERE order_id = :id");
        $upd->execute([':status' => $status, ':id' => $orderId]);
    }
    header('Location: bestellingoverzichtPersoneel.php');
    exit;
}


$stmt = $pdo->query("
    SELECT 
      o.order_id,
      o.client_name,
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


function statusOption(int $current, int $value, string $label): string {
    $sel = $current === $value ? ' selected' : '';
    return "<option value=\"$value\"$sel>$label</option>";
}
function statusLabel(int $s): string {
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
  <title>Bestellingoverzicht Personeel</title>
</head>
<body>
  <div class="wrapper">
   
    <div class="logo">
      <div class="grid-container img">
        <img src="img/Pizzeria_Sole_Machina_Logo.jpg" alt="logo">
      </div>
    </div>

    <nav>
      <ul>
        <li><a href="menu.php">menu</a></li>
        <li><a href="privacy.php">privacy verklaring</a></li>
        <li><a href="contact.php">contact</a></li>
        <li><a href="profiel.php">profiel</a></li>
        <li><a href="detailoverzichtBestelling.php">detailoverzicht bestelling</a></li>
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
      <h1>Bestellingoverzicht (Personeel)</h1>

      <?php if (empty($orders)): ?>
        <p>Er zijn geen bestellingen.</p>
      <?php else: ?>
        <table>
          <thead>
            <tr>
              <th>Order ID</th>
              <th>Klantnaam</th>
              <th>Producten</th>
              <th>Status</th>
              <th>Actie</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($orders as $ord): ?>
            <tr>
              <td><?= htmlspecialchars($ord['order_id']) ?></td>
              <td><?= htmlspecialchars($ord['client_name']) ?></td>
              <td>
                <ul>
                  <?php foreach ($ord['items'] as $it): ?>
                    <li>
                      <?= htmlspecialchars($it['product_name']) ?>
                      – <?= (int)$it['quantity'] ?>×
                    </li>
                  <?php endforeach; ?>
                </ul>
              </td>
              <td>
                <form method="POST" action="">
                  <input type="hidden" name="order_id" value="<?= (int)$ord['order_id'] ?>">
                  <select name="status">
                    <?= statusOption($ord['status'], 1, 'In behandeling') ?>
                    <?= statusOption($ord['status'], 2, 'Onderweg') ?>
                    <?= statusOption($ord['status'], 3, 'Bezorgd') ?>
                  </select>
              </td>
              <td>
                  <button type="submit" name="update">Opslaan</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>

    </div>
  </div>
</body>
</html>
