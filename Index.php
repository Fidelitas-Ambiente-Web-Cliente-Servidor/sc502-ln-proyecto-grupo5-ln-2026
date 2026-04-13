<?php 
session_start();
require_once 'conexion.php'; // Asegurar que la conexión está disponible

// Consultar estadísticas
$totalReportes = 0;
$enProceso = 0;
$resueltos = 0;

$resultTotal = $conn->query("SELECT COUNT(*) as total FROM reportes");
if ($resultTotal) $totalReportes = $resultTotal->fetch_assoc()['total'];

$resultProceso = $conn->query("SELECT COUNT(*) as total FROM reportes r JOIN estados e ON r.estado_id = e.id WHERE e.nombre IN ('en_proceso')");
if ($resultProceso) $enProceso = $resultProceso->fetch_assoc()['total'];

$resultResueltos = $conn->query("SELECT COUNT(*) as total FROM reportes r JOIN estados e ON r.estado_id = e.id WHERE e.nombre IN ('resuelto', 'cerrado')");
if ($resultResueltos) $resueltos = $resultResueltos->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoAlerta CR | Proyecto G5</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="CSS/style.css">
    <link rel="stylesheet" href="CSS/HeaderStyle.css">
    <link rel="stylesheet" href="CSS/styleFooter.css">
    <link rel="stylesheet" href="CSS/HomeStyle.css">
</head>

<body>
    <?php include 'Fragmentos/header.php'; ?>

    <main class="home-container">
        <!-- Hero / Bienvenida -->
        <section class="hero-section">
            <div class="hero-content">
                <h2>Bienvenido a EcoAlerta CR</h2>
                <p>
                    Plataforma para reportar incidentes ambientales y visualizar
                    reportes simulados en distintas zonas de Costa Rica.
                </p>
                <?php if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin'): ?>
                <div class="hero-actions">
                    <a href="http://localhost:8080/sc502-ln-proyecto-grupo5-ln-2026/Reportes/vistaReportes.php" class="btn-principal">
                        Crear reporte
                    </a>
                    <a href="/sc502-ln-proyecto-grupo5-ln-2026/Informacion/vistaInformacion.php" class="btn-secundario">
                        Más información
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Resumen / Estadísticas reales -->
        <section class="stats-section">
            <div class="stat-card">
                <h3><?php echo $totalReportes; ?></h3>
                <p>Reportes registrados</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $enProceso; ?></h3>
                <p>En proceso</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $resueltos; ?></h3>
                <p>Resueltos</p>
            </div>
            <div class="stat-card">
                <h3>7</h3>
                <p>Zonas monitoreadas</p>
            </div>
        </section>

        <!-- Mapa principal -->
        <section class="map-section">
            <div class="section-header">
                <div>
                    <h2>Mapa de reportes</h2>
                    <p>
                        Visualiza los incidentes ambientales simulados registrados en el sistema.
                    </p>
                </div>

                <div class="map-filters">
                    <select id="filtroTipo">
                        <option value="todos">Tipos</option>
                        <option value="contaminacion">Contaminación</option>
                        <option value="tala">Tala ilegal</option>
                        <option value="quema">Quema de residuos</option>
                        <option value="fauna">Daño a fauna</option>
                        <option value="agua">Contaminación de agua</option>
                    </select>

                    <select id="filtroEstado">
                        <option value="todos">Estados</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="en proceso">En proceso</option>
                        <option value="resuelto">Resuelto</option>
                    </select>
                </div>
            </div>

            <div id="mapaReportes" class="mapa-principal"></div>
        </section>

        <!-- Reportes recientes desde la BD -->
        <section class="recent-section">
            <div class="section-header">
                <div>
                    <h2>Reportes recientes</h2>
                    <p>Últimos incidentes simulados registrados en la plataforma.</p>
                </div>
            </div>

            <div class="report-list">
                <?php
                $recientes = $conn->query("
                    SELECT r.id, r.descripcion, r.fecha_hora_incidente, e.nombre as estado_nombre, c.nombre as tipo_nombre
                    FROM reportes r
                    LEFT JOIN categorias c ON r.categoria_id = c.id
                    LEFT JOIN estados e ON r.estado_id = e.id
                    ORDER BY r.fecha_hora_incidente DESC
                    LIMIT 3
                ");
                if ($recientes && $recientes->num_rows > 0):
                    while ($row = $recientes->fetch_assoc()):
                        $estadoMap = [
                            'enviado' => 'Pendiente',
                            'en_revision' => 'Pendiente',
                            'asignado' => 'Pendiente',
                            'en_proceso' => 'En proceso',
                            'resuelto' => 'Resuelto',
                            'cerrado' => 'Resuelto'
                        ];
                        $estado = $estadoMap[strtolower($row['estado_nombre'] ?? 'enviado')] ?? 'Pendiente';
                        $fecha = date('d/m/Y', strtotime($row['fecha_hora_incidente']));
                        $tipo = ucfirst($row['tipo_nombre'] ?? 'General');
                ?>
                    <article class="report-card">
                        <h3><?php echo htmlspecialchars($tipo); ?></h3>
                        <p><strong>Fecha:</strong> <?php echo $fecha; ?></p>
                        <p><strong>Estado:</strong> <?php echo $estado; ?></p>
                        <p><?php echo htmlspecialchars(substr($row['descripcion'], 0, 120)) . '…'; ?></p>
                        <a href="/sc502-ln-proyecto-grupo5-ln-2026/Reportes/detalle.php?id=<?php echo $row['id']; ?>" class="ver-mas">Ver más →</a>
                    </article>
                <?php 
                    endwhile;
                else:
                    echo '<p>No hay reportes disponibles.</p>';
                endif;
                ?>
            </div>
        </section>

        <!-- Llamado a la acción -->
        <?php if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin'): ?>
        <section class="cta-section">
            <h2>¿Deseas colaborar con el ambiente?</h2>
            <p>
                Registra un nuevo reporte y ayuda a identificar problemas ambientales
                en distintas comunidades del país.
            </p>
            <a href="http://localhost:8080/sc502-ln-proyecto-grupo5-ln-2026/Reportes/vistaReportes.php" class="btn-principal">
                Reportar ahora
            </a>
        </section>
        <?php endif; ?>
    </main>

    <?php include 'Fragmentos/footer.php'; ?>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="/sc502-ln-proyecto-grupo5-ln-2026/JS/header-footer.js"></script>
    <script src="/sc502-ln-proyecto-grupo5-ln-2026/JS/mapaIndex.js"></script>
</body>
</html>