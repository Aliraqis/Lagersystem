<?php
require_once 'config.php';

// Hämta produkt_id från $_GET-variabeln
$produkt_id = $_GET['produkt_id'];

// Förbered SQL-frågan
$stmt = $conn->prepare("SELECT batch_nummer, utgangsdatum FROM batcher WHERE produkt_id = ?");
$stmt->bind_param("i", $produkt_id);

// Exekvera SQL-frågan
$stmt->execute();

// Hämta resultatet
$result = $stmt->get_result();

// Skapa en array för batcherna
$batcher = [];

// Lägg till batcherna i arrayen
while ($row = $result->fetch_assoc()) {
  $batcher[] = $row;
}

// Stäng anslutningen till databasen
$stmt->close();
$conn->close();

// Returnera batcherna som en JSON-sträng
echo json_encode($batcher);
?>
