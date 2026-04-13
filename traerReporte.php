<?php
ini_set('display_errors', 0);
error_reporting(0);
header('Content-Type: application/json');

require_once __DIR__ . '/conexion.php';

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Sin conexión a BD']);
    exit;
}

$resultMapa = $conn->query("
    SELECT r.id, r.latitud, r.longitud, r.gravedad, r.descripcion, r.fecha_hora_incidente,
           c.nombre AS categoria, e.nombre AS estado
    FROM reportes r
    JOIN categorias c ON r.categoria_id = c.id
    JOIN estados e ON r.estado_id = e.id
    ORDER BY r.created_at DESC
");

if (!$resultMapa) {
    echo json_encode(['success' => false, 'message' => $conn->error]);
    exit;
}

$reportesMapa = [];
while ($row = $resultMapa->fetch_assoc()) $reportesMapa[] = $row;

$resultRecientes = $conn->query("
    SELECT r.id, r.descripcion, r.direccion, r.gravedad, r.created_at,
           c.nombre AS categoria, e.nombre AS estado
    FROM reportes r
    JOIN categorias c ON r.categoria_id = c.id
    JOIN estados e ON r.estado_id = e.id
    ORDER BY r.created_at DESC
    LIMIT 3
");

if (!$resultRecientes) {
    echo json_encode(['success' => false, 'message' => $conn->error]);
    exit;
}

$recientes = [];
while ($row = $resultRecientes->fetch_assoc()) $recientes[] = $row;

$total     = $conn->query("SELECT COUNT(*) AS n FROM reportes")->fetch_assoc()['n'];
$enProceso = $conn->query("SELECT COUNT(*) AS n FROM reportes r JOIN estados e ON r.estado_id = e.id WHERE e.nombre IN ('en_proceso','asignado','en_revision')")->fetch_assoc()['n'];
$resueltos = $conn->query("SELECT COUNT(*) AS n FROM reportes r JOIN estados e ON r.estado_id = e.id WHERE e.nombre IN ('resuelto','cerrado')")->fetch_assoc()['n'];
$zonas     = $conn->query("SELECT COUNT(*) AS n FROM (SELECT DISTINCT ROUND(latitud,1), ROUND(longitud,1) FROM reportes) AS z")->fetch_assoc()['n'];

echo json_encode([
    'success'   => true,
    'mapa'      => $reportesMapa,
    'recientes' => $recientes,
    'stats'     => [
        'total'      => (int)$total,
        'en_proceso' => (int)$enProceso,
        'resueltos'  => (int)$resueltos,
        'zonas'      => (int)$zonas
    ]
]);

$conn->close();
?>