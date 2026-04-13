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

    <div class="reportes-header">
        <h1>Listado de Reportes</h1>

        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="/sc502-ln-proyecto-grupo5-ln-2026/Reportes/nuevoReporte.php" class="btn-nuevo-reporte">
                + Nuevo reporte
            </a>
        <?php else: ?>
            <a href="/sc502-ln-proyecto-grupo5-ln-2026/Index.php" class="btn-nuevo-reporte">
                Iniciar sesión para crear reporte
            </a>
        <?php endif; ?>
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
                <input type="text" id="busqueda" placeholder="Buscar...">
                <button class="btn-buscar">Buscar</button>
            </div>
        </div>
    </div>

    <div class="reportes-grid" id="reportesGrid"></div>

    <div class="paginacion">
        <button class="btn-pagina" disabled>&laquo; Anterior</button>
        <span class="pagina-actual">Página 1</span>
        <button class="btn-pagina">Siguiente &raquo;</button>
    </div>

</main>

<?php include '../Fragmentos/footer.php'; ?>

<script src="/sc502-ln-proyecto-grupo5-ln-2026/JS/header-footer.js"></script>
<script src="/sc502-ln-proyecto-grupo5-ln-2026/JS/filtrosReportes.js?v=<?php echo time(); ?>"></script>

</body>
</html>