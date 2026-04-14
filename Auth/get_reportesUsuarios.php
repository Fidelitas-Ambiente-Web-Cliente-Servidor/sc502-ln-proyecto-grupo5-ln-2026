<?php
session_start();
require_once '../conexion.php'; // sube un nivel desde /Auth/

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

$usuario_id = $_SESSION['user_id'];
$estado = $_GET['estado'] ?? 'todos';
$busqueda = trim($_GET['busqueda'] ?? '');

$sql = "SELECT r.id, r.descripcion, r.fecha_hora_incidente,
               r.latitud, r.longitud,
               c.nombre AS tipo,
               e.nombre AS estado_db
        FROM reportes r
        LEFT JOIN categorias c ON r.categoria_id = c.id
        LEFT JOIN estados e ON r.estado_id = e.id
        WHERE r.usuario_id = ?";

$params = [$usuario_id];
$types = "i";

// Filtro por estado (mapeo a nombres internos)
if ($estado !== 'todos') {
    $map = [
        'pendiente' => ['enviado','en_revision'],
        'proceso'   => ['en_proceso'],
        'resuelto'  => ['resuelto','cerrado']
    ];
    if (isset($map[$estado])) {
        $placeholders = implode(',', array_fill(0, count($map[$estado]), '?'));
        $sql .= " AND e.nombre IN ($placeholders)";
        foreach ($map[$estado] as $v) {
            $params[] = $v;
            $types .= "s";
        }
    }
}

// Búsqueda por descripción
if (!empty($busqueda)) {
    $sql .= " AND r.descripcion LIKE ?";
    $params[] = "%$busqueda%";
    $types .= "s";
}

$sql .= " ORDER BY r.fecha_hora_incidente DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => 'Error en prepare: ' . $conn->error]);
    exit;
}
$stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();

$reportes = [];
while ($row = $res->fetch_assoc()) {
    $row['fecha_formateada'] = date('Y-m-d', strtotime($row['fecha_hora_incidente']));
    $mapEstado = [
        'enviado'    => 'pendiente',
        'en_revision'=> 'pendiente',
        'en_proceso' => 'proceso',
        'resuelto'   => 'resuelto',
        'cerrado'    => 'resuelto'
    ];
    $row['estado'] = $mapEstado[$row['estado_db']] ?? 'pendiente';
    $row['ubicacion'] = "Lat: {$row['latitud']}, Lng: {$row['longitud']}";
    $row['tipo'] = ucfirst($row['tipo'] ?? 'Otro');
    $reportes[] = $row;
}

echo json_encode($reportes);