<?php 
session_start(); 
include '../conexion.php';

// Obtener Usuarios
$usuariosList = [];
$res_usr = $conn->query("SELECT id, nombre, email as correo, tipo as rol, estado FROM usuarios ORDER BY id DESC");
if($res_usr){
    while($row = $res_usr->fetch_assoc()){
        $row['rol'] = ($row['rol'] === 'admin') ? 'Administrador' : 'Usuario';
        $row['estado'] = ucfirst($row['estado']); // Activo / Inactivo
        $row['id'] = intval($row['id']);
        $usuariosList[] = $row;
    }
}

// Obtener Reportes
$reportesList = [];
$qy = "SELECT r.id, c.nombre as tipo, r.usuario_id, u.nombre as usuario, r.descripcion, r.gravedad, e.nombre as estadoOriginal, r.fecha_hora_incidente, r.latitud, r.longitud, r.direccion 
       FROM reportes r
       LEFT JOIN categorias c ON r.categoria_id = c.id
       LEFT JOIN usuarios u ON r.usuario_id = u.id
       LEFT JOIN estados e ON r.estado_id = e.id
       ORDER BY r.id DESC";
$res_rep = $conn->query($qy);
if($res_rep){
    while($row = $res_rep->fetch_assoc()){
        $datetime = new DateTime($row['fecha_hora_incidente']);
        $row['fecha'] = $datetime->format('d/m/Y');
        $row['hora'] = $datetime->format('H:i');
        
        $estadoMap = [
            'enviado' => 'Pendiente',
            'en_revision' => 'Pendiente',
            'asignado' => 'Pendiente',
            'en_proceso' => 'En Proceso',
            'resuelto' => 'Resuelto',
            'cerrado' => 'Resuelto'
        ];
        $st = strtolower($row['estadoOriginal'] ?? 'enviado');
        $row['estado'] = $estadoMap[$st] ?? 'Pendiente';
        $row['ubicacion'] = $row['direccion'] ?: ($row['latitud'] . ', ' . $row['longitud']);
        $row['id'] = intval($row['id']);
        
        // --- Mejoras Visuales para el Moderador ---
        $row['usuario'] = $row['usuario'] ?: 'Anónimo';
        $row['gravedad'] = ucfirst($row['gravedad']);
        $row['tipo'] = ucfirst($row['tipo'] ?? 'General');
        
        // Evidencias (imágenes)
        $row['imagenes'] = [];
        $evQuery = $conn->query("SELECT url FROM evidencias WHERE reporte_id = {$row['id']}");
        if($evQuery) {
            while($ev = $evQuery->fetch_assoc()){
                $row['imagenes'][] = $ev['url'];
            }
        }
        $reportesList[] = $row;
    }
}

// Obtener Mensajes (contacto)
$mensajesList = [];
try {
    $res_msg = $conn->query("SELECT id, nombre, correo, asunto, mensaje, leido, estado, fecha_envio FROM mensajes WHERE estado = 'activo' ORDER BY fecha_envio DESC");
    if($res_msg){
        while($row = $res_msg->fetch_assoc()){
            $row['id'] = intval($row['id']);
            $row['leido'] = (bool)$row['leido'];
            $row['fecha_envio'] = date('d/m/Y H:i', strtotime($row['fecha_envio']));
            $mensajesList[] = $row;
        }
    }
} catch (mysqli_sql_exception $e) {
    if (strpos($e->getMessage(), "Unknown column 'estado'") !== false) {
        // La columna no existe, intentar crearla
        $conn->query("ALTER TABLE mensajes ADD COLUMN estado ENUM('activo', 'inactivo') DEFAULT 'activo'");
        // Intentar de nuevo
        $res_msg = $conn->query("SELECT id, nombre, correo, asunto, mensaje, leido, estado, fecha_envio FROM mensajes WHERE estado = 'activo' ORDER BY fecha_envio DESC");
        if($res_msg){
            while($row = $res_msg->fetch_assoc()){
                $row['id'] = intval($row['id']);
                $row['leido'] = (bool)$row['leido'];
                $row['fecha_envio'] = date('d/m/Y H:i', strtotime($row['fecha_envio']));
                $mensajesList[] = $row;
            }
        }
    } else {
        // Otra excepción, lanzar de nuevo
        throw $e;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración | EcoAlerta CR</title>

    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="../CSS/HeaderStyle.css">
    <link rel="stylesheet" href="../CSS/styleFooter.css">
    <link rel="stylesheet" href="../CSS/styleAdminPanel.css">
</head>

<body>

    <?php include '../Fragmentos/header.php'; ?>

    <!-- Modal detalle -->
    <div id="modalDetalleReporte" class="modal-detalle">
        <div class="modal-contenido">
            <span class="cerrar-modal" id="cerrarModalDetalle">&times;</span>

            <h3>Detalle del Reporte</h3>

            <div class="detalle-grid">
                <p><strong>ID:</strong> <span id="detalleId"></span></p>
                <p><strong>Tipo:</strong> <span id="detalleTipo"></span></p>
                <p><strong>Usuario:</strong> <span id="detalleUsuario"></span></p>
                <p><strong>Gravedad:</strong> <span id="detalleGravedad"></span></p>
                <p><strong>Estado:</strong> <span id="detalleEstado"></span></p>
                <p><strong>Fecha:</strong> <span id="detalleFecha"></span></p>
                <p><strong>Hora:</strong> <span id="detalleHora"></span></p>
                <p><strong>Ubicación:</strong> <span id="detalleUbicacion"></span></p>
            </div>

            <div class="detalle-bloque">
                <h4>Descripción</h4>
                <p id="detalleDescripcion"></p>
            </div>

            <div class="detalle-bloque">
                <h4>Evidencia</h4>
                <div id="detalleImagenes" class="detalle-imagenes"></div>
            </div>

            <div class="detalle-bloque">
                <h4>Mapa</h4>
                <button id="btnVerMapa" class="accion-btn mapa-btn">
                    Ver en mapa
                </button>
            </div>
        </div>
    </div>

    <main class="admin-main">

        <h2>Panel de Administración</h2>
        <p>Gestión general del sistema EcoAlerta CR</p>

        <div class="admin-tabs">
            <button class="tab-btn active" data-tab="usuarios">
                Gestión de Usuarios
            </button>

            <button class="tab-btn" data-tab="reportes">
                Moderación de Reportes
            </button>
            <button class="tab-btn" data-tab="mensajes">
                Mensajes de Contacto
            </button>

            <button class="tab-btn" data-tab="estadisticas">
                Estadísticas
            </button>
        </div>

        <!-- Seccion usuarios -->
        <section id="usuarios" class="tab-content active">
            <h3>Usuarios Registrados</h3>

            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody id="tablaUsuarios"></tbody>
            </table>
        </section>

        <!--Seccion reportes -->
        <section id="reportes" class="tab-content">
            <h3>Reportes del Sistema</h3>

            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipo</th>
                        <th>Usuario</th>
                        <th>Gravedad</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody id="tablaReportes"></tbody>
            </table>
        </section>

        <!-- Sección Mensajes -->
        <section id="mensajes" class="tab-content">
            <h3>Mensajes recibidos</h3>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Asunto</th>
                        <th>Mensaje</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaMensajes"></tbody>
            </table>
        </section>

        <!-- Sección estadísticas -->
        <section id="estadisticas" class="tab-content">
            <h3>Estadísticas del Sistema</h3>
            <div class="stats-container">
                <div class="stat-card">
                    <h4>Total Usuarios</h4>
                    <p id="totalUsuarios">0</p>
                </div>
                <div class="stat-card">
                    <h4>Total Reportes</h4>
                    <p id="totalReportes">0</p>
                </div>
                <div class="stat-card">
                    <h4>Reportes Pendientes</h4>
                    <p id="totalPendientes">0</p>
                </div>
                <div class="stat-card">
                    <h4>Reportes Resueltos</h4>
                    <p id="totalResueltos">0</p>
                </div>
                <div class="stat-card">
                    <h4>Mensajes No Leídos</h4>
                    <p id="totalNoLeidos">0</p>
                </div>
            </div>
        </section>

    </main>

    <?php include '../Fragmentos/footer.php'; ?>

    <script>
        window.usuariosIniciales = <?php echo json_encode($usuariosList); ?>;
        window.reportesIniciales = <?php echo json_encode($reportesList); ?>;
        window.mensajesIniciales = <?php echo json_encode($mensajesList); ?>;
    </script>
    <script src="/sc502-ln-proyecto-grupo5-ln-2026/JS/header-footer.js"></script>
    <script src="../JS/adminPanel.js?v=<?php echo time(); ?>"></script>

</body>
</html>