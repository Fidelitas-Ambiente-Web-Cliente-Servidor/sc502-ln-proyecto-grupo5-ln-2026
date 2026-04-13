<?php
session_start();
require_once '../conexion.php';

$id = intval($_GET['id'] ?? 0);

if (!$id) {
    header("Location: /sc502-ln-proyecto-grupo5-ln-2026/Reportes/vistaReportes.php");
    exit();
}

$stmt = $conn->prepare("
    SELECT r.id, r.descripcion, r.gravedad, r.fecha_hora_incidente, r.latitud, r.longitud,
           r.direccion, r.created_at, c.nombre AS categoria, e.nombre AS estado,
           u.nombre AS usuario
    FROM reportes r
    JOIN categorias c ON r.categoria_id = c.id
    JOIN estados e ON r.estado_id = e.id
    JOIN usuarios u ON r.usuario_id = u.id
    WHERE r.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$reporte = $stmt->get_result()->fetch_assoc();

if (!$reporte) {
    header("Location: /sc502-ln-proyecto-grupo5-ln-2026/Reportes/vistaReportes.php");
    exit();
}

$evidencias = [];
$evResult = $conn->query("SELECT url FROM evidencias WHERE reporte_id = $id");
while ($row = $evResult->fetch_assoc()) $evidencias[] = $row['url'];

$estadoMap = [
    'enviado'     => ['label' => 'Pendiente',   'class' => 'pendiente'],
    'en_revision' => ['label' => 'En revisión', 'class' => 'pendiente'],
    'asignado'    => ['label' => 'En proceso',  'class' => 'proceso'],
    'en_proceso'  => ['label' => 'En proceso',  'class' => 'proceso'],
    'resuelto'    => ['label' => 'Resuelto',    'class' => 'resuelto'],
    'cerrado'     => ['label' => 'Cerrado',     'class' => 'resuelto'],
];
$estadoInfo   = $estadoMap[$reporte['estado']] ?? ['label' => $reporte['estado'], 'class' => 'pendiente'];

$gravedadMap = [
    'baja'    => ['label' => 'Baja',    'color' => '#4caf50'],
    'media'   => ['label' => 'Media',   'color' => '#ff9800'],
    'alta'    => ['label' => 'Alta',    'color' => '#f44336'],
    'critica' => ['label' => 'Crítica', 'color' => '#b71c1c'],
];
$gravedadInfo = $gravedadMap[$reporte['gravedad']] ?? ['label' => $reporte['gravedad'], 'color' => '#666'];

$fecha         = date('d/m/Y H:i', strtotime($reporte['fecha_hora_incidente']));
$fechaCreacion = date('d/m/Y', strtotime($reporte['created_at']));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle Reporte #<?php echo $reporte['id']; ?> | EcoAlerta CR</title>
    <link rel="stylesheet" href="/sc502-ln-proyecto-grupo5-ln-2026/CSS/style.css">
    <link rel="stylesheet" href="/sc502-ln-proyecto-grupo5-ln-2026/CSS/HeaderStyle.css">
    <link rel="stylesheet" href="/sc502-ln-proyecto-grupo5-ln-2026/CSS/styleFooter.css">
    <link rel="stylesheet" href="/sc502-ln-proyecto-grupo5-ln-2026/CSS/styleVistaReportes.css">
    <link rel="stylesheet" href="/sc502-ln-proyecto-grupo5-ln-2026/CSS/styleDetalleReporte.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
</head>
<body class="page-vista-reportes">
    <?php include '../Fragmentos/header.php'; ?>

    <main class="detalle-container">

        <div class="detalle-header">
            <h1>Reporte #<?php echo $reporte['id']; ?></h1>
            <a href="/sc502-ln-proyecto-grupo5-ln-2026/Reportes/vistaReportes.php" class="btn-volver">← Volver</a>
        </div>

        <div class="detalle-card">
            <div class="detalle-badges">
                <span class="badge tipo"><?php echo htmlspecialchars($reporte['categoria']); ?></span>
                <span class="badge <?php echo $estadoInfo['class']; ?>"><?php echo $estadoInfo['label']; ?></span>
                <span class="badge" style="background:<?php echo $gravedadInfo['color']; ?>">
                    Gravedad: <?php echo $gravedadInfo['label']; ?>
                </span>
            </div>

            <div class="detalle-info">
                <div class="info-item">
                    <label>Fecha del incidente</label>
                    <p><?php echo $fecha; ?></p>
                </div>
                <div class="info-item">
                    <label>Registrado el</label>
                    <p><?php echo $fechaCreacion; ?></p>
                </div>
                <div class="info-item">
                    <label>Reportado por</label>
                    <p><?php echo htmlspecialchars($reporte['usuario']); ?></p>
                </div>
                <div class="info-item">
                    <label>Ubicación</label>
                    <p><?php echo htmlspecialchars($reporte['direccion'] ?? 'No especificada'); ?></p>
                </div>
            </div>

            <p class="seccion-titulo">Descripción</p>
            <p class="descripcion-completa"><?php echo nl2br(htmlspecialchars($reporte['descripcion'])); ?></p>
        </div>

        <div class="detalle-card">
            <p class="seccion-titulo">Ubicación en el mapa</p>
            <div id="mapaDetalle" class="mapa-detalle"></div>
        </div>

        <div class="detalle-card">
            <p class="seccion-titulo">Evidencias</p>
            <?php if (count($evidencias) > 0): ?>
                <div class="evidencias-grid">
                    <?php foreach ($evidencias as $url): ?>
                        <img src="<?php echo htmlspecialchars($url); ?>"
                             alt="Evidencia"
                             onclick="window.open(this.src)">
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="sin-evidencias">No hay evidencias adjuntas.</p>
            <?php endif; ?>
        </div>

    </main>

    <?php include '../Fragmentos/footer.php'; ?>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="/sc502-ln-proyecto-grupo5-ln-2026/JS/header-footer.js"></script>
    <script>
        const map = L.map('mapaDetalle').setView([<?php echo $reporte['latitud']; ?>, <?php echo $reporte['longitud']; ?>], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        L.marker([<?php echo $reporte['latitud']; ?>, <?php echo $reporte['longitud']; ?>])
            .addTo(map)
            .bindPopup('<b><?php echo htmlspecialchars($reporte['categoria']); ?></b>')
            .openPopup();
    </script>
</body>
</html>