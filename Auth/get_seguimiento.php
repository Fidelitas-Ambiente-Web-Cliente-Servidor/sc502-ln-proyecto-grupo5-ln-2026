<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit;
}

$userId = $_SESSION['user_id'];

// Ajusta la consulta según tu estructura real de tablas
$sql = "SELECT s.tipo, s.fecha, s.descripcion, s.usuario 
        FROM seguimiento s 
        INNER JOIN reportes r ON s.reporte_id = r.id 
        WHERE r.usuario_id = ? 
        ORDER BY s.fecha DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$seguimiento = [];
while ($row = $result->fetch_assoc()) {
    $seguimiento[] = $row;
}
header('Content-Type: application/json');
echo json_encode($seguimiento);