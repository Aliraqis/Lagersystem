<?php
require_once 'nav.php';
require_once 'config.php';
ini_set('display_errors', 1); error_reporting(E_ALL);

// Hämta produkter från databasen
$select_query = "SELECT p.id, p.varunummer, p.varunamn, p.produkt_typ, p.avdelning, p.min_antal, p.kommentar, b.batch_nummer, b.utgangsdatum, b.antal, b.produkt_id FROM produkter p INNER JOIN batcher b ON p.id = b.produkt_id";

$result = $conn->query($select_query);
?>

<!DOCTYPE html>
<html lang="sv">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TOB Lager System</title>
    
<link rel="stylesheet" type="text/css" href="style.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="main.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>

<div class="table_add">

<div class="table_header">
<p>Produkter</p>

<div class="add_new_input">
<input id="search" placeholder="sök..."/>

<button class="add_new">+ Lägg till</button></div>
</div>
</div>


<div class="produkt_table">
<div class="table_section">
    <table>
        <thead>
            <tr>
            <th data-sort="id" class="sortable">Id</th>
<th data-sort="varunummer" class="sortable">Varunummer</th>
<th data-sort="varunamn" class="sortable">Varunamn</th>
<th data-sort="batch_utg_datum" class="sortable">Batch och utg.datum</th>
<th data-sort="typ" class="sortable">Typ</th>
<th data-sort="avdelning" class="sortable">Avdelning</th>
<th data-sort="antal" class="sortable">Antal</th>
<th data-sort="min_antal" class="sortable">Min.Antal</th>
<th data-sort="kommentar" class="sortable">Kommentar</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php 
$prev_produkt_id = 0; 
while($row = $result->fetch_assoc()) { 
    $produkt_id = $row["produkt_id"];
    $batch_nummer = $row["batch_nummer"];
    $utgangsdatum = $row["utgangsdatum"];
    if ($produkt_id != $prev_produkt_id) {
        // Uppdaterat SQL-query för att räkna totalt antal från batcher-tabellen
        $totalt_antal_query = "SELECT SUM(antal) AS totalt_antal FROM batcher WHERE produkt_id = ?";
        $totalt_antal_stmt = $conn->prepare($totalt_antal_query);
        $totalt_antal_stmt->bind_param("i", $produkt_id);
        $totalt_antal_stmt->execute();
        $totalt_antal_result = $totalt_antal_stmt->get_result();
        $totalt_antal_row = $totalt_antal_result->fetch_assoc();
        $totalt_antal = $totalt_antal_row["totalt_antal"];
        ?>
        <tr data-produkt-id="<?php echo $produkt_id; ?>">
            <td><?php echo $row["id"]; ?></td>
            <td><?php echo $row["varunummer"]; ?></td>
            <td><?php echo $row["varunamn"]; ?></td>
            <td>
<select id="batch-select-<?php echo $produkt_id; ?>">

                    <?php 
                    $batch_query = "SELECT batch_nummer, utgangsdatum FROM batcher WHERE produkt_id = ?";
                    $batch_stmt = $conn->prepare($batch_query);
                    $batch_stmt->bind_param("i", $produkt_id);
                    $batch_stmt->execute();
                    $batch_result = $batch_stmt->get_result();
                    while($batch_row = $batch_result->fetch_assoc()) {
                        echo "<option value='" . $batch_row['batch_nummer'] . "'> <i class='fas fa-minus' style='color:#0aa493;'></i> " . $batch_row['batch_nummer'] . " - " . $batch_row['utgangsdatum'] . "</option>";
                    }
                    ?>
                </select>
            </td>
            <td><?php echo $row["produkt_typ"]; ?></td>
            <td><?php echo $row["avdelning"]; ?></td>
            <td><?php echo $totalt_antal; ?></td> <!-- Uppdaterad rad för att visa totala antalet från alla batcher -->
            <td><?php echo $row["min_antal"]; ?></td>
            <td><?php echo $row["kommentar"]; ?></td>
            <td>
            <button class="add-batch-btn btn_icon" data-produkt-id="<?php echo $produkt_id; ?>"><i class="fa-solid fa-plus-minus"></i></button>
            <button class="edit-produkt-btn btn_icon" data-produkt-id="<?php echo $produkt_id; ?>" data-batch-nummer="<?php echo $batch_nummer; ?>" data-utgangsdatum="<?php echo $utgangsdatum; ?>"><i class="fa-solid fa-pen-to-square"></i></button>
            <button class="delete-produkt-btn btn_icon" data-produkt-id="<?php echo $produkt_id; ?>"><i class="fa-solid fa-trash"></i></button>
            </td>
        </tr>
        <?php 
    }
    $prev_produkt_id = $produkt_id;
} 
?>

        </tbody>
    </table>
</div></div>




   <!-- Start Modal Add new Batch -->
<div class="modal">
  <div class="div_form"> 
    <div class="container_form_bach">
      
    <span class="close">&times;</span>
    <div class="title"> Lägg ny till batch</div>
      
    <form id="add-batch-form">
<div class="user-details">
    <div class="input-box">
      <label for="batch_nummer">Batchnummer:</label>
      <input type="text" id="batch_nummer" name="batch_nummer" required>
    </div>
    <div class="input-box">
      <label for="utgangsdatum">Utgångsdatum:</label>
      <input type="date" id="utgangsdatum" name="utgangsdatum" required>
    </div>

    <div class="input-box">
  <label for="antal">Antal:</label>
  <input type="number" id="antal" name="antal" required>
  </div>
    
   </div>

   <div class="mas_bach1">
      <div class="mas_bach" id="message"></div></div>
      <div class="form_Btn">
      <input type="hidden" id="produkt_id" name="produkt_id">
      <button type="submit" class="form_Btn">Lägg till batch</button>
        
        
      </div>
  
  
  </div>
  
    </form>
   
  </div>
</div>

   <!-- End Modal Add new Batch -->



   <!-- Start Modal Edit Batch -->
<div class="modal-edit">
  <div class="div_form"> 
    <div class="container_form_bach">
      
      <span class="close-edit">&times;</span>
      <div class="title"> Redigera batch</div>
        
      <form id="edit-batch-form">
        <div class="user-details">
          <div class="input-box">
            <label for="edit-batch-nummer">Batchnummer:</label>
            <input type="text" id="edit-batch-nummer" name="batch_nummer" required>
          </div>
          <div class="input-box">
            <label for="edit-utgangsdatum">Utgångsdatum:</label>
            <input type="date" id="edit-utgangsdatum" name="utgangsdatum" required>
          </div>

          <div class="input-box">
            <label for="edit-antal">Antal:</label>
            <input type="number" id="edit-antal" name="antal" required>
          </div>
          
        </div>

        <div class="mas_bach1">
          <div class="mas_bach" id="edit-message"></div></div>
          <div class="form_Btn">
          <input type="hidden" id="edit-produkt-id" name="produkt_id">
          <button type="submit" class="form_Btn">Spara ändringar</button>
          </div>
    
      </div>
    
      </form>
     
    </div>
  </div>
</div>
<!-- End Modal Edit Batch -->

   

</body></html>
