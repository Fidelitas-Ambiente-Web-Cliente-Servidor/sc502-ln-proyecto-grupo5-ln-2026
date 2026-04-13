<?php
include 'conexion.php';
$conn->query("ALTER TABLE mensajes ADD COLUMN estado ENUM('activo', 'inactivo') DEFAULT 'activo'");
$conn->query("ALTER TABLE mensajes ADD COLUMN leido BOOLEAN DEFAULT FALSE");
echo "Done";
?>
