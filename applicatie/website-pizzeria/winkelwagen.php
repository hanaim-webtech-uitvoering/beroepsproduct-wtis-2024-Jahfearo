<?php
session_start();


$bericht = '';
require_once('//__db_connectie.php');
require_once('//functions.php');
$db = '';


if(isset($_POST['cmdWinkelmandje'])){

    if($_POST['cmdWinkelmandje'] == "Update bestelling"){
        session_unset();
        $dataWinkelmandje = $_POST['winkelmandje'];
        foreach($dataWinkelmandje as $productnaam => $aantal){
            if($aantal > 0){
            $_SESSION['winkelmandje'][$productnaam] = $aantal;
            }
        }
    } else{
        //code om de bestelling te plaatsen
        $voornaam = $_POST['voornaam'];
        $adres = $_POST['adres'];
        $bestelling = $_POST['winkelmandje'];
        if($bestelling == $_SESSION['winkelmandje']){

            //wanneer niemand is ingelogd
            $sql = "INSERT INTO Pizza_Order
    (client_username, client_name, personnel_username, datetime, status, address)
    VALUES(NULL, $voornaam, 'wbos', datetime(), '1', $adres);";

            $db = maakVerbinding();
            $query = $db->prepare($sql);
            $query->execute([]);
        }
    }
}

/*
//opbouw van tijdelijke sessiegegevens.
$_SESSION['winkelmandje']['Coca Cola'] = 4;
$_SESSION['winkelmandje']['Sprite'] = 3;
$_SESSION['winkelmandje']['Knoflookbrood'] = 1;


Winkelmandje uit sessie op basis van string

$winkelmandje = "Coca Cola-4,Sprite-3,Knofloopbrood-1";
$producten = explode(",", $winkelmandje);

$productenlijst = '<table>';
foreach($producten as $product){
    $productInformatie = explode("-", $product);
    $productenlijst .= '<tr>';
    $productenlijst .= '<td>';  
    $productenlijst .= $productInformatie[0];
    $productenlijst .= '</td>';
    $productenlijst .= '<td>';   
    $productenlijst .=     $productInformatie[1];
    $productenlijst .= '</td>';
    $productenlijst .= '</tr>';
}
$productenlijst .= '</table>';
*/


/*
Winkelmandje uit sessie op basis van Non-Associatief Array
$winkelmandje = [['Coca Cola', 4],['Sprite', 3],['Knoflookbrood', 1]];

$productenlijst = '<table>';
foreach($winkelmandje as $product){
    $productenlijst .= '<tr>';
    $productenlijst .= '<td>';  
    $productenlijst .= $productInformatie[0];
    $productenlijst .= '</td>';
    $productenlijst .= '<td>';   
    $productenlijst .=     $productInformatie[1];
    $productenlijst .= '</td>';
    $productenlijst .= '</tr>';
}
$productenlijst .= '</table>';
*/

if(isset($_SESSION['winkelmandje'])){
//hier komt code als er wel iets in het winkelmandje staat.
$db = maakVerbinding();

$viewWinkelmand = '';
$viewWinkelmandItems = '';
$totaalPrijs = 0;

$dataWinkelmandje = $_SESSION['winkelmandje'];
foreach($dataWinkelmandje as $productnaam => $aantal){
    $productInformatie = getProductInformatie($productnaam, $db);
    $subtotaal = ($aantal * $productInformatie['price']);
    $totaalPrijs = $totaalPrijs + $subtotaal;
    $viewWinkelmandItems .= '
            <tr>
                <td><img src="https://placehold.co/200x180/png" alt="'.$productnaam.'"> Informatie en iets over de '.$productnaam.'</td>
                <td><input type="number" name="winkelmandje['.$productnaam.']" value="'.$aantal.'"></td>
                <td>'.moneyformat($productInformatie['price']).'</td>
                <td>'.moneyformat($subtotaal).'</td>
            </tr>
    ';
    
}

$btw = ($totaalPrijs /100 * 9);
$viewWinkelmand .= '
<form action="winkelwagen.php" method="post">
        <table>
            <tr>
                <th>Product</th>
                <th>Aantal</th>
                <th>Prijs</th>
                <th>Subtotaal</th>
            </tr>
            '.$viewWinkelmandItems.'
            <tr><td colspan="4" style="background-color: #D32F2F;"></td></tr>
            <tr>
                <td colspan="3" style="text-align: right;">Subtotaal : </td>
                <td>'.moneyformat(($totaalPrijs - $btw)).'</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right;">BTW : </td>
                <td>'.moneyformat($btw).'</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right;">Totaal : </td>
                <td style="background-color: #D32F2F;  font-weight: 700;">'.moneyformat($totaalPrijs).'</td>
            </tr>
            <tr><td colspan="4" style="background-color: #D32F2F;"></td></tr>

        </table>

                <input type="text" name="voornaam" placeholder="Voornaam">
        <input type="text" name="adres" placeholder="6511JB, 11">
        <input type="submit" name="cmdWinkelmandje" value="Update bestelling">
        <input type="submit" name="cmdWinkelmandje" value="Plaats bestelling">
       </form>
';

$bericht = $viewWinkelmand;
}
else{
//hier is de winkelmand leeg.
$bericht = 'De winkelmand is leeg.';
}

?>
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
            <h1>Uw winkelwagen</h1>
            <div class="grid-container">
                <div class="cart">
                    <h1>Winkelwagen</h1>
                    <ul class="cart-items">
                        <li>🍕 Pizza Margherita - 2x</li>
                        <li>🥤 Coca-Cola 330ml - 2x</li>
                    </ul>
                    <p><strong>Totaalbedrag:</strong> €25,40</p>
                    <p><strong>Opmerkingen:</strong> Geen speciale opmerkingen</p>
                    <button class="order-button">Bestellen</button>
                </div>
            </div>
        </div>        
    </div>
</body>
</html>