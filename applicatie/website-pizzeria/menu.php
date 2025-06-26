<?php

session_start();
require_once __DIR__ . '/../db_connectie.php';
$pdo = maakVerbinding();

$melding = '';


if (isset($_POST['cmdWinkelmandje']) && $_POST['cmdWinkelmandje'] === 'Add') {
    $naam = $_POST['productnaam'];
    $qty  = max(1, min(50, (int)$_POST['quantity'])); 

    $_SESSION['winkelmandje'][$naam] = 
        ($_SESSION['winkelmandje'][$naam] ?? 0) + $qty;

    $melding = "$qty × \"$naam\" toegevoegd aan je winkelwagen.";
}


$stmtTypes = $pdo->query("
    SELECT [name] AS naam
    FROM ProductType
    ORDER BY [name]
");
$types = $stmtTypes->fetchAll();


$stmtProd = $pdo->prepare("
    SELECT
      p.[name]       AS productnaam,
      p.price        AS prijs,
      STRING_AGG(i.[name], ', ')
        WITHIN GROUP (ORDER BY i.[name]) AS ingrediënten
    FROM Product p
    LEFT JOIN Product_Ingredient pi
      ON p.[name] = pi.product_name
    LEFT JOIN Ingredient i
      ON pi.ingredient_name = i.[name]
    WHERE p.type_id = :typeNaam
    GROUP BY p.[name], p.price
    ORDER BY p.[name]
");
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="normalize.css">
  <link rel="stylesheet" href="styles.css">
  <title>Pizzeria Sole Machina - Menu</title>
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
      <h1>Ons assortiment</h1>

      <?php if ($melding): ?>
        <p style="color:green; font-weight:bold;"><?= htmlspecialchars($melding) ?></p>
      <?php endif; ?>

      <div class="grid-container">
        <?php foreach ($types as $type): ?>
          <?php
            $typeNaam = $type['naam'];
            $stmtProd->execute([':typeNaam' => $typeNaam]);
            $producten = $stmtProd->fetchAll();
          ?>
          <section class="menu-type">
            <h2><?= htmlspecialchars($typeNaam) ?></h2>

            <?php if (empty($producten)): ?>
              <p>Geen <?= htmlspecialchars(strtolower($typeNaam)) ?> beschikbaar.</p>
            <?php else: ?>
              <?php foreach ($producten as $product): ?>
                <div class="menu-item">
                  <div class="menu-content">
                    <h3>
                      <?= htmlspecialchars($product['productnaam']) ?>
                      <span class="price">
                        € <?= number_format($product['prijs'], 2, ',', '.') ?>
                      </span>
                    </h3>
                    <?php if ($product['ingrediënten']): ?>
                      <p class="details"><?= htmlspecialchars($product['ingrediënten']) ?></p>
                    <?php endif; ?>
                  </div>
                  <img
                    src="img/<?= rawurlencode(str_replace(' ', '', $product['productnaam'])) ?>.jpg"
                    alt="<?= htmlspecialchars($product['productnaam']) ?>">

                  <form method="post" class="order-form">
                    <input type="hidden" name="cmdWinkelmandje" value="Add">
                    <input type="hidden" name="productnaam" value="<?= htmlspecialchars($product['productnaam']) ?>">
                    <input type="number" name="quantity" class="quantity-input" min="1" max="50" value="1">
                    <button type="submit" class="add-button">+</button>
                  </form>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>

          </section>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</body>
</html>
