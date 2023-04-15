<?php
require_once 'nav.php';
require_once 'config.php';

$message_form = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $varunummer = $_POST["varunummer"];
    $varunamn = $_POST["varunamn"];
    $batch_nummer = $_POST["batch_nummer"];
    $utgangsdatum = $_POST["utgangsdatum"];
    $produkt_typ = $_POST["produkt_typ"];
    $avdelning = $_POST["avdelning"];
    $antal = $_POST["antal"];
    $min_antal = $_POST["min_antal"];
    $lagerplats = $_POST["lagerplats"];
    $kommentar = $_POST["kommentar"];

    // Kontrollera om produkten redan finns i databasen
    $check_query = "SELECT * FROM produkter WHERE varunummer = ? OR varunamn = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ss", $varunummer, $varunamn);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $message_form = "Produkten finns redan.";
    } else {
        // Lägg till produkten i databasen
        $insert_query = "INSERT INTO produkter (varunummer, varunamn, produkt_typ, avdelning, lagerplats, min_antal, kommentar) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("sssssss", $varunummer, $varunamn, $produkt_typ, $avdelning, $lagerplats, $min_antal, $kommentar);
        $stmt->execute();

        // Hämta det nyligen inlagda produktens ID
        $product_id = $stmt->insert_id;

        // Lägg till batchen i databasen
        $insert_query = "INSERT INTO batcher (produkt_id, batch_nummer, utgangsdatum, antal) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("isss", $product_id, $batch_nummer, $utgangsdatum, $antal);
        $stmt->execute();

        $message_form = "Produkten har lagts till.";
    }

    // Stäng anslutningen
    $stmt->close();
    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="sv">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TOB Lager System</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    
<div class="div_title">
    <div class="title">Lägg till ny produkt</div></div>


   <div class="div_form"> 
    <div class="container_form">
   
<div class="massage_div">
    <?php if (!empty($message_form)): ?>
                <div class="message_form"><?php echo $message_form; ?></div>
            <?php endif; ?></div>

    <form action="add_product.php" method="post">

<div class="user-details">
    <div class="input-box">
        <label for="varunummer">Varunummer:</label>
        <input type="text" name="varunummer" id="varunummer"  required>
    </div>

    <div class="input-box">
        <label for="varunamn">Varunamn:</label>
        <input type="text" name="varunamn" id="varunamn" required>
    </div>

    <div class="input-box">
        <label for="batch_nummer">Batch nummer:</label>
        <input type="text" name="batch_nummer" id="batch_nummer" required>
    </div>

    <div class="input-box">
        <label for="utgangsdatum">Utgångsdatum:</label>
        <input type="date" name="utgangsdatum" id="utgangsdatum" required>
    </div>

    <div class="input-box">
        <label for="produkt_typ">Typ av produkt:</label>
        <select name="produkt_typ" id="produkt_typ">
        <option value="" disabled selected hidden>Välj avdelning</option>
            <option value="läkemedel">Läkemedel</option>
            <option value="förbrukningsmaterial">Förbrukningsmaterial</option>
        </select>

    </div>

    <div class="input-box">
        <label for="avdelning">Avdelning:</label>
        <select name="avdelning" id="avdelning" >
        <option value="" disabled selected hidden>Välj typ </option>
            <option value="Steril">Steril</option>
            <option value="Cytostatika">Cytostatika</option>
            <option value="Båda">Båda</option>
        </select></div>

        <div class="input-box">
        <label for="antal">Antal:</label>
        <input type="number" name="antal" id="antal" required></div>

        <div class="input-box">
        <label for="min_antal">Minimum Antal:</label>
        <input type="number" name="min_antal" id="min_antal" required></div>

        <div class="input-box">
        <label for="lagerplats">Lagerplats:</label>
        <input type="text" name="lagerplats" id="lagerplats" required></div>

        <div class="input-box">
        <label for="kommentar">Kommentar:</label>
        <input type="text" name="kommentar" id="kommentar"  ></div>


        

</div><div class="form_Btn">
        <input type="submit" name="submit" value="Lägg till produkt">
    </div>
    </form></div></div>
</body>
</html>
