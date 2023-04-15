<?php
// config.php
ini_set('display_errors', 1); error_reporting(E_ALL);
// Databasanslutningsinställningar
$servername = "localhost"; // Servernamn, ändra till din server om det behövs
$username = "Apoex";    // Användarnamn, ändra till ditt databasanvändarnamn
$password = "Mila2020Maja";    // Lösenord, ändra till ditt databaslösenord
$dbname = "lagersystem";   // Databasnamn, behåll samma namn som i CREATE DATABASE

// Skapa anslutning
$conn = new mysqli($servername, $username, $password, $dbname);

// Kontrollera anslutning
if ($conn->connect_error) {
  die("Anslutning misslyckades: " . $conn->connect_error);
}


?>
