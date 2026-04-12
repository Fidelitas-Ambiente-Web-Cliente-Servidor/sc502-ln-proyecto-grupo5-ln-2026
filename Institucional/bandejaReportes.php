<?php 
session_start();

// Validación de usuario administrador
/* Temporalmente comentado para pruebas
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: /sc502-ln-proyecto-grupo5-ln-2026/Index.php");
    exit();
}
*/

include '../conexion.php';

// Fetch reportes
$queryReportes = "SELECT r.id, c.nombre as tipo, r.fecha_hora_incidente, e.nombre as estadoOriginal, r.descripcion, r.gravedad,
                  (SELECT url FROM evidencias WHERE reporte_id = r.id LIMIT 1) as foto
                  FROM reportes r 
                  LEFT JOIN categorias c ON r.categoria_id = c.id
                  LEFT JOIN estados e ON r.estado_id = e.id
                  ORDER BY r.id DESC";
$resReportes = $conn->query($queryReportes);
$reportes = [];
if($resReportes){
    while($row = $resReportes->fetch_assoc()){
        $datetime = new DateTime($row['fecha_hora_incidente']);
        $row['fecha'] = $datetime->format('d/m/Y');
        
        $estadoMap = [
            'enviado' => 'Pendiente',
            'en_revision' => 'Pendiente',
            'asignado' => 'Pendiente',
            'en_proceso' => 'En proceso',
            'resuelto' => 'Resuelto',
            'cerrado' => 'Cerrado'
        ];
        $st = strtolower($row['estadoOriginal'] ?? 'enviado');
        $row['estado'] = $estadoMap[$st] ?? 'Pendiente';
        
        $row['tipo'] = ucfirst($row['tipo'] ?? 'Otro');
        $row['gravedad'] = ucfirst($row['gravedad'] ?? 'Media');
        
        $reportes[] = $row;
    }
}

// Fetch instituciones
$queryInst = "SELECT id, nombre FROM instituciones";
$resInst = $conn->query($queryInst);
$instituciones = [];
if ($resInst && $resInst->num_rows > 0) {
    while($row = $resInst->fetch_assoc()){
        $instituciones[] = $row;
    }
} else {
    // Auto-popular instituciones si la BD está vacía
    $conn->query("INSERT INTO instituciones (nombre) VALUES ('MINAE'), ('SINAC'), ('AyA'), ('Municipalidad Local')");
    $resInst = $conn->query($queryInst);
    if($resInst) {
        while($row = $resInst->fetch_assoc()){
            $instituciones[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Zona Institucional</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="../CSS/HeaderStyle.css">
    <link rel="stylesheet" href="../CSS/styleFooter.css">
    <link rel="stylesheet" href="../CSS/PerfilStyle.css">
</head>

<body class="page-perfil">

    <?php include '../Fragmentos/header.php'; ?>

    <main class="perfil-container">

            <div class="perfil-header">
                <div class="perfil-info">
                    <h2>Zona Institucional</h2>
                </div>
            </div>

            <div class="perfil-tabs">
                <button class="tab-link active" data-tab="bandeja">Bandeja de reportes</button>
                <button class="tab-link" data-tab="asignacion">Asignación</button>
                <button class="tab-link" data-tab="estado">Actualizar estado</button>
                <button class="tab-link" data-tab="comentarios">Comentarios</button>
                <button class="tab-link" data-tab="cerrar">Cerrar caso</button>
            </div>

            <!-- Bandeja -->
            <div id="bandeja" class="tab-content active">
                <h3>Bandeja de reportes</h3>
                <div class="reportes-lista">
                    <?php if (empty($reportes)): ?>
                        <p>No hay reportes disponibles.</p>
                    <?php else: ?>
                        <?php foreach($reportes as $r): ?>
                        <div class="reporte-card" 
                             data-id="<?php echo str_pad($r['id'], 2, '0', STR_PAD_LEFT); ?>" 
                             data-tipo="<?php echo htmlspecialchars($r['tipo']); ?>" 
                             data-fecha="<?php echo $r['fecha']; ?>" 
                             data-desc="<?php echo htmlspecialchars($r['descripcion']); ?>" 
                             data-gravedad="<?php echo htmlspecialchars($r['gravedad']); ?>"
                             data-foto="<?php echo htmlspecialchars($r['foto'] ?? ''); ?>">
                            <div>
                                <h4>Reporte #<?php echo str_pad($r['id'], 2, '0', STR_PAD_LEFT); ?></h4>
                                <p>Tipo: <?php echo htmlspecialchars($r['tipo']); ?></p>
                                <p>Fecha: <?php echo $r['fecha']; ?></p>
                            </div>
                            <div>
                                <span class="estado <?php echo strtolower(str_replace(' ', '-', $r['estado'])); ?>"><?php echo $r['estado']; ?></span>
                                <button class="btn-guardar ver-detalle" style="padding: 5px 10px; font-size: 12px; margin-top:5px;">Ver</button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Asignación -->
            <div id="asignacion" class="tab-content">
                <h3>Asignación de reportes</h3>
                <div class="reportes-lista">
                    <?php foreach($reportes as $r): ?>
                    <div class="reporte-card">
                        <div>
                            <h4>Reporte #<?php echo $r['id']; ?></h4>
                            <p>Tipo: <?php echo htmlspecialchars($r['tipo']); ?></p>
                        </div>
                        <div>
                            <label>Asignar a:</label>
                            <select class="asignarInstitucion">
                                <option value="">Seleccione una institución</option>
                                <?php foreach($instituciones as $inst): ?>
                                    <option value="<?php echo $inst['id']; ?>"><?php echo htmlspecialchars($inst['nombre']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button class="btn-guardar btn-asignar" data-id="<?php echo $r['id']; ?>">Asignar</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Actualizar estado -->
            <div id="estado" class="tab-content">
                <h3>Actualizar estado del reporte</h3>
                <div class="input-group">
                    <label>Seleccione reporte</label>
                    <select id="selectReporteEstado">
                        <?php foreach($reportes as $r): ?>
                        <option value="<?php echo $r['id']; ?>">Reporte #<?php echo $r['id']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-group">
                    <label>Nuevo estado</label>
                    <select id="nuevoEstadoReporte">
                        <option value="Pendiente">Pendiente</option>
                        <option value="En Proceso">En proceso</option>
                        <option value="Resuelto">Resuelto</option>
                    </select>
                </div>
                <button class="btn-guardar" id="btnActualizarEstado">Actualizar estado</button>
            </div>

            <!-- Comentarios -->
            <div id="comentarios" class="tab-content">
                <h3>Agregar comentario</h3>
                <div class="input-group">
                    <label>Seleccione reporte</label>
                    <select id="selectReporteComentario">
                        <?php foreach($reportes as $r): ?>
                        <option value="<?php echo $r['id']; ?>">Reporte #<?php echo $r['id']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-group">
                    <label>Comentario</label>
                    <textarea id="textoComentario" rows="4" placeholder="Escriba el comentario"></textarea>
                </div>
                <button class="btn-guardar" id="btnGuardarComentario">Guardar comentario</button>
            </div>

            <!-- Cerrar Caso -->
            <div id="cerrar" class="tab-content">
                <h3>Cerrar caso</h3>
                <div class="input-group">
                    <label>Seleccione reporte</label>
                    <select id="selectReporteCerrar">
                        <?php foreach($reportes as $r): ?>
                        <option value="<?php echo $r['id']; ?>">Reporte #<?php echo $r['id']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button class="btn-guardar" id="btnCerrarCaso">Cerrar caso</button>
            </div>

    </main>

    <!-- Modal Detalle -->
    <div id="modalDetalleBandeja" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); justify-content: center; align-items: center; z-index: 1000;">
        <div class="modal-content" style="background: white; padding: 25px; border-radius: 16px; width: 500px; max-width: 90%; position: relative;">
            <button id="cerrarModalBandeja" style="position: absolute; top: 15px; right: 15px; background: #e74c3c; color: white; border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer; font-weight: bold;">X</button>
            <h3 id="modalTitulo" style="margin-top: 0; color: #2ecc71;">Detalle de Reporte</h3>
            <p><strong>Tipo:</strong> <span id="modalTipo"></span></p>
            <p><strong>Fecha:</strong> <span id="modalFecha"></span></p>
            <p><strong>Gravedad:</strong> <span id="modalGravedad"></span></p>
            <p><strong>Descripción:</strong> <span id="modalDesc"></span></p>
            <div id="modalImagenContainer" style="margin-top: 15px; text-align: center;">
                <img id="modalImagen" src="" alt="Evidencia" style="max-width: 100%; border-radius: 12px; display: none; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                <p id="modalNoImagen" style="color: #7f8c8d; font-style: italic; display: none;">Sin evidencia fotográfica.</p>
            </div>
        </div>
    </div>

    <?php include '../Fragmentos/footer.php'; ?>
    
    <script src="/sc502-ln-proyecto-grupo5-ln-2026/JS/header-footer.js"></script>
    <script src="../JS/institucional.js?v=<?php echo time(); ?>"></script>

</body>
</html>