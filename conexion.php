<?php
// Datos de conexión local a MySQL.
$host = "localhost";
$user = "root";
$pass = "";
$db   = "d6guitars"; // Nombre de la base de datos del proyecto.

// Crea la conexión usando MySQLi.
$conn = new mysqli($host, $user, $pass, $db);

// Detiene la ejecución si la conexión falla.
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
