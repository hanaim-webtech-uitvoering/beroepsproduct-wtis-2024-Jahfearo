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
                <li><a href="contact.html">contact</a></li>
                <li><a href="detailoverzichtBestelling.html">detailoverzicht bezorgingen</a></li>
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
            <h1>Bestellingoverzicht</h1>
            <div class="grid-container">
                <section>
                    <h2>Bestelling Details</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Bestelling ID</th>
                                <th>Klantnaam</th>
                                <th>Product</th>
                                <th>Hoeveelheid</th>
                                <th>Status</th>
                                <th>Acties</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Jan Jansen</td>
                                <td>Pizza Margherita</td>
                                <td>2</td>
                                <td>
                                    <select>
                                        <option>In behandeling</option>
                                        <option>Geleverd</option>
                                        <option>Geannuleerd</option>
                                    </select>
                                </td>
                                <td><button>Opslaan</button></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Piet Pietersen</td>
                                <td>Pizza Salami</td>
                                <td>1</td>
                                <td>
                                    <select>
                                        <option>In behandeling</option>
                                        <option>Geleverd</option>
                                        <option>Geannuleerd</option>
                                    </select>
                                </td>
                                <td><button>Opslaan</button></td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Marie de Boer</td>
                                <td>Pizza Salami</td>
                                <td>3</td>
                                <td>
                                    <select>
                                        <option>In behandeling</option>
                                        <option>Geleverd</option>
                                        <option>Geannuleerd</option>
                                    </select>
                                </td>
                                <td><button>Opslaan</button></td>
                            </tr>
                        </tbody>
                    </table>
                </section>
            </div>
        </div>
    </div>
</body>
</html>
