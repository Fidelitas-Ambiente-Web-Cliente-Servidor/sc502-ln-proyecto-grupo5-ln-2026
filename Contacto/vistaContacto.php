<?php session_start(); ?>
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>
<?php include '../conexion.php'; ?>
<?php
$mensajeExito = '';
$mensajeError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre  = trim($_POST['nombre']  ?? '');
    $correo  = trim($_POST['correo']  ?? '');
    $asunto  = trim($_POST['asunto']  ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');

    if (!$nombre || !$correo || !$asunto || !$mensaje) {
        $mensajeError = 'Por favor complete todos los campos.';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensajeError = 'El correo electrónico no es válido.';
    } else {
        $stmt = $conn->prepare("
            INSERT INTO mensajes (nombre, correo, asunto, mensaje)
            VALUES (?, ?, ?, ?)
        ");

        if (!$stmt) {
            $mensajeError = 'Error prepare: ' . $conn->error;
        } else {
            $stmt->bind_param("ssss", $nombre, $correo, $asunto, $mensaje);

            if (!$stmt->execute()) {
                $mensajeError = 'Error execute: ' . $stmt->error;
            } else {
                $stmt->close();
                $mensajeExito = '¡Mensaje enviado correctamente! Nos pondremos en contacto pronto.';
            }
        }
    }
}
?>

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

            <?php if ($mensajeExito): ?>
                <div class="mensaje-confirmacion exito"><?= htmlspecialchars($mensajeExito) ?></div>
            <?php endif; ?>

            <?php if ($mensajeError): ?>
                <div class="mensaje-confirmacion error"><?= htmlspecialchars($mensajeError) ?></div>
            <?php endif; ?>

            <form id="formContacto" class="contacto-form" method="POST" action="">

                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre"
                           placeholder="Ingrese su nombre"
                           value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="correo">Correo electrónico</label>
                    <input type="email" id="correo" name="correo"
                           placeholder="correo@ejemplo.com"
                           value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="asunto">Asunto</label>
                    <input type="text" id="asunto" name="asunto"
                           placeholder="Motivo del mensaje"
                           value="<?= htmlspecialchars($_POST['asunto'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="mensaje">Mensaje</label>
                    <textarea id="mensaje" name="mensaje" rows="5"
                              placeholder="Escriba su mensaje aquí"><?= htmlspecialchars($_POST['mensaje'] ?? '') ?></textarea>
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