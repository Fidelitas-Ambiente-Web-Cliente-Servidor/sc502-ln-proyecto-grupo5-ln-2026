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
$queryReportes = "SELECT r.id, c.nombre as tipo, r.fecha_hora_incidente, e.nombre as estadoOriginal, r.descripcion, r.gravedad 
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
                             data-gravedad="<?php echo htmlspecialchars($r['gravedad']); ?>">
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

    <?php include '../Fragmentos/footer.php'; ?>
    
    <script src="/sc502-ln-proyecto-grupo5-ln-2026/JS/header-footer.js"></script>
    <script src="../JS/institucional.js?v=<?php echo time(); ?>"></script>

</body>
</html>