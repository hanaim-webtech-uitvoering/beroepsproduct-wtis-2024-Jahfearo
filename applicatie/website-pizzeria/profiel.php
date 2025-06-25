<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="normalize.css">
    <link rel="stylesheet" href="styles.css">
    <title>Pizzeria Sole Machina</title>
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
                <li><a href="menu.html">menu</a></li>
                <li><a href="privacy.html">privacy verklaring</a></li>
                <li><a href="contact.html">contact </a></li>
            </ul>
        </nav>
        <div class="thuispagina"><a href="index.html">thuispagina</a></div>
        <div class="profiel"><a href="profiel.html">profiel</a></div>
        <div class="winkelwagen"><a href="winkelwagen.html">winkelwagen</a></div>
        <div class="reclamelinks"><strong>famillie pakket:</strong> Bestel 3 pizza's en krijg 1 gratis drankje 
            <div class="grid-container img">
            <img src="img/pizzamagerita.jpg" alt="pizza Margherita">
            <img src="img/Coca-Cola-Sleek-33cl-Blik-200x667.png" alt="blikje Coca-Cola"></div></div>
        <div class="reclamerechts"><strong>pizza woensdag</strong> alle pizzas 20% korting op woensdag
            <div class="grid-container img">
            <img src="img/pizzasalami.jpg" alt="pizza salami"></div></div>
        <div class="footer"></div>
        <div class="content"> 
            
            <div class="grid-container">
                <div class="login-wrapper">
                    <h1>Inloggen/Registreren</h1>
                    
                    <section class="login">
                        <h2>Klant Login</h2>
                        <form method="POST" action="klantPagina.html">
                            <label for="klant-email">E-mailadres</label>
                            <input type="email" id="klant-email" name="klant-email" required>
                            
                            <label for="klant-password">Wachtwoord</label>
                            <input type="password" id="klant-password" name="klant-password" required>
                            
                            <input type="submit" value="Inloggen als Klant"> <input type="submit" value="Registreren als Klant">
                        </form>
                    </section>
                    
                    <section class="login">
                        <h2>Personeel Login</h2>
                        <form method="POST" action="personeelsPagina.html">
                            <label for="personeel-email">E-mailadres</label>
                            <input type="email" id="personeel-email" name="personeel-email" required>
                            
                            <label for="personeel-password">Wachtwoord</label>
                            <input type="password" id="personeel-password" name="personeel-password" required>
                            
                            <label for="personeelsid">Personeels-ID</label>
                            <input type="text" id="personeelsid" name="personeelsid" required>
                            
                            <input type="submit" value="Inloggen als Personeel">
                        </form>
                    </section>
                </div>
            </div>
        </div>        
    </div>
</body>
</html>