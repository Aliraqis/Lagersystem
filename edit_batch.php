<?php
require_once 'config.php';
header('Content-Type: application/json');


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $produkt_id = $_POST["produkt_id"];
    $batch_nummer = $_POST["batch_nummer"];
    $utgangsdatum = $_POST["utgangsdatum"];
    $antal = $_POST["antal"];

    // Uppdatera batch i databasen
    $update_query = "UPDATE batcher SET batch_nummer = ?, utgangsdatum = ?, antal = ? WHERE produkt_id = ? AND batch_nummer = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssisi", $batch_nummer, $utgangsdatum, $antal, $produkt_id, $batch_nummer);

    if ($stmt->execute()) {
        $response = [
            "message" => "Batchen med nummer $batch_nummer för produkt med ID $produkt_id har uppdaterats med utgångsdatum $utgangsdatum och antal $antal.",
            "success" => true,
        ];
        echo json_encode($response);
    } else {
        $response = [
            "message" => "Ett fel uppstod när batchen skulle uppdateras. Försök igen senare.",
            "success" => false,
        ];
        echo json_encode($response);
    }

    $stmt->close();
    $conn->close();
}
?>
