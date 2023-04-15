<?php
ob_start();
require_once 'config.php';

$produkt_id = $_POST['produkt_id'];
$batch_nummer = $_POST['batch_nummer'];
$utgangsdatum = $_POST['utgangsdatum'];
$antal = $_POST['antal'];

$response = array('success' => false, 'message' => '');

// Kontrollera om en batch med samma produkt_id och batch_nummer redan finns
$check_query = "SELECT * FROM batcher WHERE produkt_id = ? AND batch_nummer = ?";
$check_stmt = $conn->prepare($check_query);

if (!$check_stmt) {
    error_log("Prepare failed: " . $conn->error);
} else {
    $check_stmt->bind_param("is", $produkt_id, $batch_nummer);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows == 0) {
        // Om ingen batch med samma produkt_id och batch_nummer finns, lägg till en ny
        $query = "INSERT INTO batcher (produkt_id, batch_nummer, utgangsdatum, antal) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
        } else {
            $stmt->bind_param("isss", $produkt_id, $batch_nummer, $utgangsdatum, $antal);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "Batchen har lagts till!";
            } else {
                error_log("Execute failed: " . $stmt->error);
                $response['message'] = "Ett fel inträffade. Försök igen!";
            }

            $stmt->close();
        }
    } else {
        $response['message'] = "Batchen finns redan!";
        error_log("Batch with the same produkt_id and batch_nummer already exists.");
    }

    $check_stmt->close();
}

$conn->close();

ob_clean();
echo json_encode($response);
?>
