<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Reportes | EcoAlerta CR</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="../CSS/HeaderStyle.css">
    <link rel="stylesheet" href="../CSS/styleFooter.css">
    <link rel="stylesheet" href="../CSS/styleVistaReportes.css">
</head>
<body class="page-vista-reportes">
    <?php include '../Fragmentos/header.php'; ?>

    <main class="reportes-container">
        <!-- Cabecera -->
        <div class="reportes-header">
            <h1>Listado de Reportes</h1>
            <a href="/sc502-ln-proyecto-grupo5-ln-2026/Reportes/nuevoReporte.php" class="btn-nuevo-reporte">
                + Nuevo reporte
            </a>
        </div>

        <!-- Filtros -->
        <div class="filtros-section">
            <div class="filtros-grid">
                <select id="filtroTipo">
                    <option value="">Todos los tipos</option>
                    <option value="contaminacion">Contaminación</option>
                    <option value="tala">Tala ilegal</option>
                    <option value="quema">Quema de residuos</option>
                    <option value="fauna">Daño a fauna</option>
                    <option value="agua">Contaminación de agua</option>
                    <option value="otro">Otro</option>
                </select>

                <select id="filtroEstado">
                    <option value="">Todos los estados</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="proceso">En proceso</option>
                    <option value="resuelto">Resuelto</option>
                </select>

                <div class="busqueda-wrapper">
                    <input type="text" id="busqueda" placeholder="Buscar por ubicación o descripción...">
                    <button class="btn-buscar">Buscar</button>
                </div>
            </div>
        </div>

        <!-- Listado de reportes -->
        <div class="reportes-grid" id="reportesGrid">
            <!-- Tarjetas de ejemplo (luego se cargaran dinamicamente) -->
            <article class="reporte-card">
                <div class="card-header">
                    <span class="tipo">Contaminación</span>
                    <span class="estado pendiente">Pendiente</span>
                </div>
                <h3>Contaminación de río</h3>
                <p class="ubicacion">Heredia, Costa Rica</p>
                <p class="descripcion">
                    Se reporta presencia de residuos sólidos y posible contaminación del agua en una quebrada cercana.
                </p>
                <div class="card-footer">
                    <span class="fecha">2026-02-15</span>
                    <a href="#" class="ver-detalle">Ver detalle →</a>
                </div>
            </article>

            <article class="reporte-card">
                <div class="card-header">
                    <span class="tipo">Tala ilegal</span>
                    <span class="estado proceso">En proceso</span>
                </div>
                <h3>Tala ilegal en Alajuela</h3>
                <p class="ubicacion">Alajuela, Costa Rica</p>
                <p class="descripcion">
                    Se detectó la tala de varios árboles en una zona protegida sin autorización visible.
                </p>
                <div class="card-footer">
                    <span class="fecha">2026-02-14</span>
                    <a href="#" class="ver-detalle">Ver detalle →</a>
                </div>
            </article>

            <article class="reporte-card">
                <div class="card-header">
                    <span class="tipo">Quema de residuos</span>
                    <span class="estado resuelto">Resuelto</span>
                </div>
                <h3>Quema en lote baldío</h3>
                <p class="ubicacion">San José, Costa Rica</p>
                <p class="descripcion">
                    Vecinos reportaron humo constante por la quema de basura en un lote baldío.
                </p>
                <div class="card-footer">
                    <span class="fecha">2026-02-10</span>
                    <a href="#" class="ver-detalle">Ver detalle →</a>
                </div>
            </article>
        </div>

        <!-- Paginacion simple -->
        <div class="paginacion">
            <button class="btn-pagina" disabled>&laquo; Anterior</button>
            <span class="pagina-actual">Página 1 de 1</span>
            <button class="btn-pagina">Siguiente &raquo;</button>
        </div>

    </main>

    <?php include '../Fragmentos/footer.php'; ?>

    <script src="/sc502-ln-proyecto-grupo5-ln-2026/JS/header-footer.js"></script>
    <script src="/sc502-ln-proyecto-grupo5-ln-2026/JS/filtrosReportes.js"></script>
</body>
</html>