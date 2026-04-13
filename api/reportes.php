<?php
header('Content-Type: application/json');
require_once '../conexion.php';

$sql = "SELECT 
            r.id, 
            r.latitud, 
            r.longitud, 
            r.descripcion,
            r.gravedad,
            r.fecha_hora_incidente,
            c.nombre AS tipo_nombre,
            e.nombre AS estado_nombre
        FROM reportes r
        LEFT JOIN categorias c ON r.categoria_id = c.id
        LEFT JOIN estados e ON r.estado_id = e.id
        WHERE r.latitud IS NOT NULL AND r.longitud IS NOT NULL
        ORDER BY r.fecha_hora_incidente DESC";

$result = $conn->query($sql);
$reportes = [];

while ($row = $result->fetch_assoc()) {
    // Mapear estado a texto amigable
    $estadoMap = [
        'enviado' => 'Pendiente',
        'en_revision' => 'Pendiente',
        'asignado' => 'Pendiente',
        'en_proceso' => 'En proceso',
        'resuelto' => 'Resuelto',
        'cerrado' => 'Resuelto'
    ];
    $estadoOriginal = $row['estado_nombre'] ?? 'enviado';
    $estadoTexto = $estadoMap[strtolower($estadoOriginal)] ?? 'Pendiente';

    $reportes[] = [
        'id' => $row['id'],
        'lat' => floatval($row['latitud']),
        'lng' => floatval($row['longitud']),
        'tipo' => ucfirst($row['tipo_nombre'] ?? 'General'),
        'estado' => $estadoTexto,
        'descripcion' => $row['descripcion'],
        'fecha' => date('d/m/Y H:i', strtotime($row['fecha_hora_incidente']))
    ];
}

echo json_encode($reportes);
?>