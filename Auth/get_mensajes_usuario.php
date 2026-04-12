<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email'])) {
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

include '../conexion.php';

$email = $_SESSION['user_email'];

// Solo mostramos mensajes activos (no eliminados)
$stmt = $conn->prepare("SELECT id, asunto, mensaje, leido, fecha_envio FROM mensajes WHERE correo = ? AND estado = 'activo' ORDER BY fecha_envio DESC");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$mensajes = [];
while ($row = $result->fetch_assoc()) {
    $row['fecha_envio'] = date('d/m/Y H:i', strtotime($row['fecha_envio']));
    $row['leido'] = (bool)$row['leido'];
    $mensajes[] = $row;
}
$stmt->close();

echo json_encode($mensajes);
?>