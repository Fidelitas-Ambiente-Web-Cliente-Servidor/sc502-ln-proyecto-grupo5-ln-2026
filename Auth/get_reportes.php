<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit;
}

$userId = $_SESSION['user_id'];
$estado = isset($_GET['estado']) ? $_GET['estado'] : 'todos';
$busqueda = isset($_GET['busqueda']) ? '%' . $_GET['busqueda'] . '%' : '%';

$sql = "SELECT id, fecha, estado, descripcion FROM reportes WHERE usuario_id = ?";
$params = [$userId];
$types = "i";

if ($estado !== 'todos') {
    $sql .= " AND estado = ?";
    $params[] = $estado;
    $types .= "s";
}
if (!empty($busqueda) && $busqueda !== '%%') {
    $sql .= " AND descripcion LIKE ?";
    $params[] = $busqueda;
    $types .= "s";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$reportes = [];
while ($row = $result->fetch_assoc()) {
    $reportes[] = $row;
}
header('Content-Type: application/json');
echo json_encode($reportes);