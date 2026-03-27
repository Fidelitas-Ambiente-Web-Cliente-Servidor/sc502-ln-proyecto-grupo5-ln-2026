<?php session_start(); ?>
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

            <div id="bandeja" class="tab-content active">

                <h3>Bandeja de reportes</h3>

                <div class="reportes-lista">

                    <div class="reporte-card">
                        <div>
                            <h4>Reporte #01</h4>
                            <p>Tipo: Contaminación</p>
                            <p>Fecha: 01/03/2026</p>
                        </div>
                        <div>
                            <span class="estado pendiente">Pendiente</span>
                        </div>
                    </div>

                    <div class="reporte-card">
                        <div>
                            <h4>Reporte #02</h4>
                            <p>Tipo: Tala ilegal</p>
                            <p>Fecha: 02/03/2026</p>
                        </div>
                        <div>
                            <span class="estado pendiente">Pendiente</span>
                        </div>
                    </div>

                </div>

            </div>

            <div id="asignacion" class="tab-content">

                <h3>Asignación de reportes</h3>

                <div class="reportes-lista">

                    <div class="reporte-card">

                        <div>
                            <h4>Reporte #01</h4>
                            <p>Tipo: Contaminación</p>
                        </div>

                        <div>

                            <label>Asignar a:</label>

                            <select id="asignarUsuario">
                                <option value="">Seleccione un usuario</option>
                                <option value="user1">Usuario 1</option>
                                <option value="user2">Usuario 2</option>
                                <option value="user3">Usuario 3</option>
                            </select>

                            <button class="btn-guardar">Asignar</button>

                        </div>

                    </div>

                </div>

            </div>

            <div id="estado" class="tab-content">

                <h3>Actualizar estado del reporte</h3>

                <div class="input-group">
                    <label>Seleccione reporte</label>
                    <select>
                        <option>Reporte #01</option>
                        <option>Reporte #02</option>
                    </select>
                </div>

                <div class="input-group">
                    <label>Nuevo estado</label>
                    <select>
                        <option>Pendiente</option>
                        <option>En proceso</option>
                        <option>Resuelto</option>
                    </select>
                </div>

                <button class="btn-guardar">Actualizar estado</button>

            </div>

            <div id="comentarios" class="tab-content">

                <h3>Agregar comentario</h3>

                <div class="input-group">
                    <label>Seleccione reporte</label>
                    <select>
                        <option>Reporte #01</option>
                        <option>Reporte #02</option>
                    </select>
                </div>

                <div class="input-group">
                    <label>Comentario</label>
                    <textarea rows="4" placeholder="Escriba el comentario"></textarea>
                </div>

                <button class="btn-guardar">Guardar comentario</button>

            </div>

            <div id="cerrar" class="tab-content">

                <h3>Cerrar caso</h3>

                <div class="input-group">
                    <label>Seleccione reporte</label>
                    <select>
                        <option>Reporte #01</option>
                        <option>Reporte #02</option>
                    </select>
                </div>

                <button class="btn-guardar">Cerrar caso</button>

            </div>

        </main>

        <?php include '../Fragmentos/footer.php'; ?>
        
        <script src="/sc502-ln-proyecto-grupo5-ln-2026/JS/header-footer.js"></script>
        <script src="../JS/institucional.js"></script>

</body>

</html>