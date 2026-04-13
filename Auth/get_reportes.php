<?php
session_start();
require_once '../conexion.php';

header('Content-Type: application/json');

$tipo = $_GET['tipo'] ?? 'todos';
$estado = $_GET['estado'] ?? 'todos';
$busqueda = trim($_GET['busqueda'] ?? '');

$sql = "SELECT r.id, r.descripcion, r.fecha_hora_incidente,
               r.latitud, r.longitud,
               c.nombre AS tipo,
               e.nombre AS estado_db
        FROM reportes r
        LEFT JOIN categorias c ON r.categoria_id = c.id
        LEFT JOIN estados e ON r.estado_id = e.id
        WHERE 1=1";

$params = [];
$types = "";

// FILTRO TIPO
if ($tipo !== 'todos') {
    $sql .= " AND LOWER(c.nombre) LIKE ?";
    $params[] = "%$tipo%";
    $types .= "s";
}

// FILTRO ESTADO
if ($estado !== 'todos') {
    $map = [
        'pendiente' => ['enviado','en_revision'],
        'proceso' => ['en_proceso'],
        'resuelto' => ['resuelto','cerrado']
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

// BUSQUEDA
if (!empty($busqueda)) {
    $sql .= " AND r.descripcion LIKE ?";
    $params[] = "%$busqueda%";
    $types .= "s";
}

// ORDEN
$sql .= " ORDER BY r.fecha_hora_incidente DESC";

// DEBUG
error_log("SQL: $sql");
error_log("Params: " . json_encode($params));

// PREPARE SEGURO
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        'error' => 'Error en prepare',
        'detalle' => $conn->error
    ]);
    exit;
}

// BIND solo si hay params
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

// EXECUTE SEGURO
if (!$stmt->execute()) {
    echo json_encode([
        'error' => 'Error en execute',
        'detalle' => $stmt->error
    ]);
    exit;
}

$res = $stmt->get_result();

$reportes = [];

while ($row = $res->fetch_assoc()) {

    $row['fecha_formateada'] = date('Y-m-d', strtotime($row['fecha_hora_incidente']));

    $mapEstado = [
        'enviado' => 'pendiente',
        'en_revision' => 'pendiente',
        'en_proceso' => 'proceso',
        'resuelto' => 'resuelto',
        'cerrado' => 'resuelto'
    ];

    $row['estado'] = $mapEstado[$row['estado_db']] ?? 'pendiente';
    $row['ubicacion'] = "Lat: {$row['latitud']}, Lng: {$row['longitud']}";
    $row['tipo'] = ucfirst($row['tipo'] ?? 'Otro');

    $reportes[] = $row;
}

echo json_encode([
    'reportes' => $reportes,
    'total' => count($reportes)
]);