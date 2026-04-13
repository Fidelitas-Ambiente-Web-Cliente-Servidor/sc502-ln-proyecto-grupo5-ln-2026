<?php
require_once 'conexion.php';
$res = $conn->query("SELECT * FROM categorias");
while($row = $res->fetch_assoc()) {
    print_r($row);
}
echo "REPORTES:\n";
$res2 = $conn->query("SELECT * FROM reportes");
while($row = $res2->fetch_assoc()) {
    print_r($row);
}
?>
