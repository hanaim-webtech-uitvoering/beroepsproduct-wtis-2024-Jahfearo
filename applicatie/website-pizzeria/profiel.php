<?php
//inloggen & registreren pagina
session_start();
if (isset($_SESSION['username'])) {
    header('Location: klantPagina.php');
    exit;
}

require_once __DIR__ . '/../db_connectie.php';
$pdo = maakVerbinding();

$foutLogin       = '';
$foutRegister    = '';
$successRegister = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username   = trim($_POST['username']    ?? '');
    $password   = trim($_POST['password']    ?? '');
    $first_name = trim($_POST['first_name']  ?? '');
    $last_name  = trim($_POST['last_name']   ?? '');
    $address    = trim($_POST['address']     ?? '');

    if (isset($_POST['login'])) {
        if (!$username || !$password) {
            $foutLogin = "Vul gebruikersnaam en wachtwoord in.";
        } else {
            $stmt = $pdo->prepare("
                SELECT username, password, role
                FROM users
                WHERE username = :username
            ");
            $stmt->execute([':username' => $username]);
            $gebruiker = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($gebruiker) {
                $hash = $gebruiker['password'];
                $ok = password_verify($password, $hash);

                
                if (! $ok
                    && substr($hash, 0, 4) !== '$2y$'
                    && $password === $hash
                ) {
                    $ok = true;
                    $nieuweHash = password_hash($password, PASSWORD_DEFAULT);
                    $upd = $pdo->prepare("
                        UPDATE users
                        SET password = :ph
                        WHERE username = :u
                    ");
                    $upd->execute([
                        ':ph' => $nieuweHash,
                        ':u'  => $username
                    ]);
                }

                if ($ok) {
                    $_SESSION['username'] = $gebruiker['username'];
                    $_SESSION['role']     = $gebruiker['role'];
                    header("Location: klantPagina.php");
                    exit;
                }
            }
            $foutLogin = "Ongeldige gebruikersnaam of wachtwoord.";
        }
    }

    
    if (isset($_POST['register'])) {
        if (!$username || !$password || !$first_name || !$last_name) {
            $foutRegister = "Vul alle verplichte velden in (naam & wachtwoord).";
        } else {
            $check = $pdo->prepare("SELECT 1 FROM users WHERE username = :username");
            $check->execute([':username' => $username]);
            if ($check->fetch()) {
                $foutRegister = "Gebruikersnaam bestaat al.";
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $insert = $pdo->prepare("
                    INSERT INTO users 
                      (username, password, first_name, last_name, address, role)
                    VALUES 
                      (:username, :password, :first_name, :last_name, :address, 'Client')
                ");
                $insert->execute([
                    ':username'   => $username,
                    ':password'   => $hashed,
                    ':first_name' => $first_name,
                    ':last_name'  => $last_name,
                    ':address'    => $address
                ]);
                $successRegister = "Registratie gelukt! Je kunt nu inloggen.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="normalize.css">
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="auth.css">
  <title>Inloggen &amp; Registreren – Pizzeria Sole Machina</title>
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
      <h1>Account</h1>
      <div class="auth-container">
        
        <div class="auth-box">
          <h2>Inloggen</h2>
          <?php if ($foutLogin): ?>
            <p class="error"><?= htmlspecialchars($foutLogin) ?></p>
          <?php endif; ?>
          <form method="POST" action="">
            <div class="form-group">
              <label for="login_username">Gebruikersnaam</label>
              <input type="text" id="login_username" name="username" required>
            </div>
            <div class="form-group">
              <label for="login_password">Wachtwoord</label>
              <input type="password" id="login_password" name="password" required>
            </div>
            <div class="form-buttons">
              <input type="submit" name="login" value="Inloggen">
            </div>
          </form>
        </div>

        
        <div class="auth-box">
          <h2>Registreren</h2>
          <?php if ($foutRegister): ?>
            <p class="error"><?= htmlspecialchars($foutRegister) ?></p>
          <?php elseif ($successRegister): ?>
            <p class="success"><?= htmlspecialchars($successRegister) ?></p>
          <?php endif; ?>
          <form method="POST" action="">
            <div class="form-group">
              <label for="reg_username">Gebruikersnaam *</label>
              <input type="text" id="reg_username" name="username" required>
            </div>
            <div class="form-group">
              <label for="reg_password">Wachtwoord *</label>
              <input type="password" id="reg_password" name="password" required>
            </div>
            <div class="form-group">
              <label for="reg_first_name">Voornaam *</label>
              <input type="text" id="reg_first_name" name="first_name" required>
            </div>
            <div class="form-group">
              <label for="reg_last_name">Achternaam *</label>
              <input type="text" id="reg_last_name" name="last_name" required>
            </div>
            <div class="form-group">
              <label for="reg_address">Adres (optioneel)</label>
              <input type="text" id="reg_address" name="address"
                     placeholder="Straatnaam 1, 1234AB Stad">
            </div>
            <div class="form-buttons">
              <input type="submit" name="register" value="Registreren">
            </div>
          </form>
        </div>
      </div>
    </div> 

  </div> 
</html>