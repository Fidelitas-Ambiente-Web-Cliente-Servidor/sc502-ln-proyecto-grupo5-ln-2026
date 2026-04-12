<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

include '../conexion.php';

// Obtener datos del usuario logueado (si existe)
$user_nombre = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '';
$user_email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : '';

$mensajeExito = '';
$mensajeError = '';

if (!$conn) {
    $mensajeError = 'Error de conexión a la base de datos.';
} else {
    $checkTable = $conn->query("SHOW TABLES LIKE 'mensajes'");
    if ($checkTable->num_rows == 0) {
        $mensajeError = 'La tabla "mensajes" no existe en la base de datos.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$mensajeError) {
    // Si el usuario envía el formulario, permitimos que pueda modificar los datos (no forzamos los de sesión)
    $nombre  = trim($_POST['nombre']  ?? '');
    $correo  = trim($_POST['correo']  ?? '');
    $asunto  = trim($_POST['asunto']  ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');

    if (!$nombre || !$correo || !$asunto || !$mensaje) {
        $mensajeError = 'Por favor complete todos los campos.';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensajeError = 'El correo electrónico no es válido.';
    } else {
        $stmt = $conn->prepare("INSERT INTO mensajes (nombre, correo, asunto, mensaje) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            $mensajeError = 'Error en la preparación: ' . $conn->error;
        } else {
            $stmt->bind_param("ssss", $nombre, $correo, $asunto, $mensaje);
            if ($stmt->execute()) {
                $mensajeExito = '¡Mensaje enviado correctamente! Nos pondremos en contacto pronto.';
                $_POST = []; // Limpiar POST después del éxito
            } else {
                $mensajeError = 'Error al guardar: ' . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Contacto | EcoAlerta CR</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="../CSS/HeaderStyle.css">
    <link rel="stylesheet" href="../CSS/styleFooter.css">
    <link rel="stylesheet" href="../CSS/styleContacto.css">
</head>
<body>
    <?php include '../Fragmentos/header.php'; ?>
    <main class="contacto-container">
        <section class="contacto-header">
            <h2>Contáctanos</h2>
            <p>Si tienes dudas, sugerencias o deseas comunicar un problema relacionado con la plataforma EcoAlerta CR, puedes enviarnos un mensaje.</p>
        </section>

        <section class="contacto-form-section">
            <?php if ($mensajeExito): ?>
                <div class="mensaje-confirmacion exito"><?= htmlspecialchars($mensajeExito) ?></div>
            <?php endif; ?>
            <?php if ($mensajeError): ?>
                <div class="mensaje-confirmacion error"><?= htmlspecialchars($mensajeError) ?></div>
            <?php endif; ?>

            <form class="contacto-form" method="POST" action="">
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" 
                           placeholder="Ingrese su nombre" 
                           value="<?= htmlspecialchars($_POST['nombre'] ?? $user_nombre) ?>">
                </div>
                <div class="form-group">
                    <label for="correo">Correo electrónico</label>
                    <input type="email" id="correo" name="correo" 
                           placeholder="correo@ejemplo.com" 
                           value="<?= htmlspecialchars($_POST['correo'] ?? $user_email) ?>">
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
                <button type="submit" class="btn-principal">Enviar mensaje</button>
            </form>
        </section>

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
</body>
</html>