<?php
require_once 'config.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

$query = $_GET['query'];

if ($query) {
    $result = $conn->query($query);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data);
} else {
    echo json_encode(['error' => 'Query is not defined']);
}

?>
