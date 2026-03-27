<?php
$servername = "db";
$usernameDB = "appuser";
$passwordDB = "apppass";
$dbname = "appdb";

$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

// Opcional: comprobar que la base de datos se seleccionó correctamente
if (!$conn->select_db($dbname)) {
    die("Error seleccionando la base de datos: " . $conn->error);
}
?>