<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Reporte</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="../CSS/HeaderStyle.css">
    <link rel="stylesheet" href="../CSS/styleFooter.css">
    <link rel="stylesheet" href="../CSS/styleReportes.css">

    <!-- Mapa -->
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    />
</head>

<body class="page-reportes">
    <?php include '../Fragmentos/header.php'; ?>

    <main class="perfil-container">
        <div class="perfil-header">
            <div class="perfil-info">
                <h2>Crear reporte</h2>
            </div>
        </div>

        <div class="tab-content active">
            <h3>Datos del reporte</h3>

            <form id="formReporte">

                <div class="input-group">
                    <label for="tipoProblema">Tipo de problema</label>
                    <select id="tipoProblema" required>
                        <option value="">Seleccione una opción</option>
                        <option value="contaminacion">Contaminación</option>
                        <option value="tala">Tala ilegal</option>
                        <option value="quema">Quema de residuos</option>
                        <option value="fauna">Daño a fauna</option>
                        <option value="agua">Contaminación de agua</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>

                <div class="input-group">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" rows="5" required></textarea>
                </div>

                <div class="input-group">
                    <label for="fecha">Fecha</label>
                    <input type="date" id="fecha" required>
                </div>

                <div class="input-group">
                    <label for="hora">Hora</label>
                    <input type="time" id="hora" required>
                </div>

                <div class="input-group">
                    <label for="gravedad">Gravedad</label>
                    <select id="gravedad" required>
                        <option value="">Seleccione el nivel</option>
                        <option value="baja">Baja</option>
                        <option value="media">Media</option>
                        <option value="alta">Alta</option>
                        <option value="critica">Crítica</option>
                    </select>
                </div>

                <div class="input-group">
                    <label for="imagenes">Subir imágenes</label>
                    <input type="file" id="imagenes" accept="image/*" multiple>
                </div>

                <div class="input-group">
                    <label>Vista previa</label>
                    <div id="previewImagenes"></div>
                </div>

                <div class="input-group">
                    <label>Ubicación del incidente</label>
                    <div id="mapa" style="height: 350px; border-radius: 16px;"></div>
                </div>

                <button type="submit" class="btn-guardar">
                    Enviar reporte
                </button>

            </form>
        </div>

    </main>

    <?php include '../Fragmentos/footer.php'; ?>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="/sc502-ln-proyecto-grupo5-ln-2026/JS/header-footer.js"></script>
    <script src="../JS/reportes.js?v=<?php echo time(); ?>"></script>

</body>
</html>