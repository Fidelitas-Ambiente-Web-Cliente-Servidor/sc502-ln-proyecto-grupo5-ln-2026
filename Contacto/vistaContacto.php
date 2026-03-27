<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto | EcoAlerta CR</title>

    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="../CSS/HeaderStyle.css">
    <link rel="stylesheet" href="../CSS/styleFooter.css">
    <link rel="stylesheet" href="../CSS/styleContacto.css">
</head>

<body>

    <?php include '../Fragmentos/header.php'; ?>

    <main class="contacto-container">

        <!-- Encabezado -->
        <section class="contacto-header">
            <h2>Contáctanos</h2>
            <p>
                Si tienes dudas, sugerencias o deseas comunicar un problema relacionado
                con la plataforma EcoAlerta CR, puedes enviarnos un mensaje.
            </p>
        </section>

        <!-- Formulario -->
        <section class="contacto-form-section">

            <form id="formContacto" class="contacto-form">

                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" placeholder="Ingrese su nombre">
                </div>

                <div class="form-group">
                    <label for="correo">Correo electrónico</label>
                    <input type="email" id="correo" placeholder="correo@ejemplo.com">
                </div>

                <div class="form-group">
                    <label for="asunto">Asunto</label>
                    <input type="text" id="asunto" placeholder="Motivo del mensaje">
                </div>

                <div class="form-group">
                    <label for="mensaje">Mensaje</label>
                    <textarea id="mensaje" rows="5" placeholder="Escriba su mensaje aquí"></textarea>
                </div>

                <button type="submit" class="btn-principal">
                    Enviar mensaje
                </button>

            </form>

            <div id="mensajeConfirmacion" class="mensaje-confirmacion"></div>

        </section>

        <!-- Información -->
        <section class="contacto-info">

            <h3>Información de contacto</h3>

            <div class="info-cards">

                <div class="info-card">
                    <h4>Correo</h4>
                    <p>contacto@ecoalerta.cr</p>
                </div>

                <div class="info-card">
                    <h4>Teléfono</h4>
                    <p>+506 8888 8888</p>
                </div>

                <div class="info-card">
                    <h4>Ubicación</h4>
                    <p>San José, Costa Rica</p>
                </div>

            </div>

        </section>

    </main>

    <?php include '../Fragmentos/footer.php'; ?>

    <script src="/sc502-ln-proyecto-grupo5-ln-2026/JS/header-footer.js"></script>
    <script src="/sc502-ln-proyecto-grupo5-ln-2026/JS/contacto.js"></script>

</body>

</html>