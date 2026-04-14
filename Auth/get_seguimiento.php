<?php
session_start();
require_once '../conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

$userId = $_SESSION['user_id'];

$sql = "SELECT 
            s.id,
            s.reporte_id,
            s.comentario,
            s.fecha_cambio,
            e.nombre AS estado_nombre,
            u.nombre AS usuario_nombre,
            c.nombre AS tipo_reporte
        FROM seguimiento_estados s
        INNER JOIN reportes r ON s.reporte_id = r.id
        INNER JOIN categorias c ON r.categoria_id = c.id
        INNER JOIN estados e ON s.estado_id = e.id
        INNER JOIN usuarios u ON s.usuario_id = u.id
        WHERE r.usuario_id = ?
        ORDER BY s.fecha_cambio DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$seguimiento = [];
while ($row = $result->fetch_assoc()) {
    $fecha = date('d/m/Y H:i', strtotime($row['fecha_cambio']));
    $tipo = (!empty($row['comentario'])) ? 'comentario' : 'cambio';
    
    if ($tipo == 'comentario') {
        $descripcion = "Comentario: " . htmlspecialchars($row['comentario']);
    } else {
        $descripcion = "Estado cambiado a: " . ucfirst(str_replace('_', ' ', $row['estado_nombre']));
    }
    
    $seguimiento[] = [
        'tipo' => $tipo,
        'fecha' => $fecha,
        'descripcion' => $descripcion,
        'usuario' => $row['usuario_nombre'],
        'estado' => $row['estado_nombre'],
        'reporte_id' => (int)$row['reporte_id'],
        'tipo_reporte' => ucfirst($row['tipo_reporte'])
    ];
}

echo json_encode($seguimiento);