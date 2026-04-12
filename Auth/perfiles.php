<?php
session_start();
require_once '../conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: InicioS.php");
    exit;
}

$userId = $_SESSION['user_id'];
$userType = $_SESSION['user_type'];
$userName = $_SESSION['user_name'];
$userEmail = $_SESSION['user_email'];

$stmt = $conn->prepare("SELECT nombre, email, telefono, fecha_registro FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($nombre, $email, $telefono, $fechaRegistro);
$stmt->fetch();
$stmt->close();

if (!$nombre) {
    session_destroy();
    header("Location: InicioS.php");
    exit;
}

// Actualizar datos personales
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $nuevoNombre = trim($_POST['nombre']);
    $nuevoEmail = trim($_POST['email']);
    $nuevoTelefono = trim($_POST['telefono']);

    $errors = [];
    if (empty($nuevoNombre)) $errors[] = "El nombre es obligatorio";
    if (empty($nuevoEmail) || !filter_var($nuevoEmail, FILTER_VALIDATE_EMAIL)) $errors[] = "Email inválido";
    if (empty($nuevoTelefono) || !preg_match('/^\d+$/', $nuevoTelefono) || strlen($nuevoTelefono) < 8) $errors[] = "Teléfono inválido (mínimo 8 dígitos)";

    if (empty($errors) && $nuevoEmail !== $email) {
        $check = $conn->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
        $check->bind_param("si", $nuevoEmail, $userId);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) $errors[] = "El correo ya está registrado por otro usuario";
        $check->close();
    }

    if (empty($errors)) {
        $update = $conn->prepare("UPDATE usuarios SET nombre = ?, email = ?, telefono = ? WHERE id = ?");
        $update->bind_param("sssi", $nuevoNombre, $nuevoEmail, $nuevoTelefono, $userId);
        if ($update->execute()) {
            $_SESSION['user_name'] = $nuevoNombre;
            $_SESSION['user_email'] = $nuevoEmail;
            $nombre = $nuevoNombre;
            $email = $nuevoEmail;
            $telefono = $nuevoTelefono;
            $success = "Datos actualizados correctamente";
        } else {
            $errors[] = "Error al actualizar: " . $conn->error;
        }
        $update->close();
    }

    $_SESSION['profile_errors'] = $errors;
    $_SESSION['profile_success'] = $success ?? null;
    header("Location: perfiles.php");
    exit;
}

// Cambiar contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    $actual = $_POST['actual'];
    $nueva = $_POST['nueva'];
    $confirmar = $_POST['confirmar'];

    $errors = [];
    $stmt = $conn->prepare("SELECT contraseña FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($hashed);
    $stmt->fetch();
    $stmt->close();

    if (!password_verify($actual, $hashed)) {
        $errors[] = "La contraseña actual es incorrecta";
    } elseif (strlen($nueva) < 6) {
        $errors[] = "La nueva contraseña debe tener al menos 6 caracteres";
    } elseif ($nueva !== $confirmar) {
        $errors[] = "Las contraseñas no coinciden";
    }

    if (empty($errors)) {
        $newHash = password_hash($nueva, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE usuarios SET contraseña = ? WHERE id = ?");
        $update->bind_param("si", $newHash, $userId);
        if ($update->execute()) {
            $success = "Contraseña actualizada correctamente";
        } else {
            $errors[] = "Error al actualizar contraseña";
        }
        $update->close();
    }

    $_SESSION['profile_errors'] = $errors;
    $_SESSION['profile_success'] = $success ?? null;
    header("Location: perfiles.php");
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: /sc502-ln-proyecto-grupo5-ln-2026/Index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Perfil | EcoAlerta CR</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="../CSS/HeaderStyle.css">
    <link rel="stylesheet" href="../CSS/styleFooter.css">
    <link rel="stylesheet" href="../CSS/PerfilStyle.css">
</head>
<body class="page-perfil">
    <?php include '../Fragmentos/header.php'; ?>

    <main class="perfil-container">
        <div class="perfil-header">
            <div class="perfil-avatar">
                <img src="/sc502-ln-proyecto-grupo5-ln-2026/img/image.png" alt="Avatar" id="avatar-img">
                <button class="btn-avatar" id="cambiarAvatar">Cambiar foto</button>
            </div>
            <div class="perfil-info">
                <h2 id="nombreUsuario"><?php echo htmlspecialchars($nombre); ?></h2>
                <p id="emailUsuario"><?php echo htmlspecialchars($email); ?></p>
                <p class="miembro-desde">Miembro desde: <span id="fechaRegistro"><?php echo date('d/m/Y', strtotime($fechaRegistro)); ?></span></p>
            </div>
        </div>

        <?php if (isset($_SESSION['profile_success'])): ?>
            <div class="alert alert-success">
                <span class="alert-icon">✓</span>
                <span class="alert-message"><?php echo $_SESSION['profile_success']; ?></span>
            </div>
            <?php unset($_SESSION['profile_success']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['profile_errors']) && count($_SESSION['profile_errors']) > 0): ?>
            <div class="alert alert-error">
                <span class="alert-icon">⚠</span>
                <ul class="alert-message-list">
                    <?php foreach ($_SESSION['profile_errors'] as $err): ?>
                        <li><?php echo htmlspecialchars($err); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['profile_errors']); ?>
        <?php endif; ?>

        <div class="perfil-tabs">
            <button class="tab-link active" data-tab="datos">Mis Datos</button>
            <button class="tab-link" data-tab="reportes">Mis Reportes</button>
            <button class="tab-link" data-tab="seguimiento">Seguimiento</button>
            <button class="tab-link" data-tab="mensajes">Mis Mensajes</button>   <!-- NUEVO -->
            <button class="tab-link" data-tab="password">Cambiar Contraseña</button>
        </div>

        <div id="datos" class="tab-content active">
            <h3>Información personal</h3>
            <form id="formDatos" action="perfiles.php" method="post">
                <input type="hidden" name="action" value="update_profile">
                <div class="input-group">
                    <label for="nombre">Nombre completo</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>">
                </div>
                <div class="input-group">
                    <label for="email">Correo electrónico</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
                </div>
                <div class="input-group">
                    <label for="telefono">Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" value="<?php echo htmlspecialchars($telefono); ?>">
                </div>
                <button type="submit" class="btn-guardar">Guardar cambios</button>
            </form>
        </div>

        <div id="reportes" class="tab-content">
            <h3>Mis reportes ambientales</h3>
            <div class="filtros-reportes">
                <select id="filtroEstadoReporte">
                    <option value="todos">Estados</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="proceso">En proceso</option>
                    <option value="resuelto">Resuelto</option>
                </select>
                <input type="text" id="busquedaReporte" placeholder="Buscar por descripción...">
                <button id="btnBuscarReporte">Buscar</button>
            </div>
            <div class="reportes-lista" id="listaReportes"></div>
            <button class="btn-nuevo-reporte" id="btnNuevoReporte">+ Nuevo reporte</button>
        </div>

        <div id="seguimiento" class="tab-content">
            <h3>Seguimiento y bitácora</h3>
            <div class="timeline" id="timeline"></div>
        </div>

        <!-- NUEVA SECCIÓN: Mensajes enviados -->
        <div id="mensajes" class="tab-content">
            <h3>Mis mensajes de contacto</h3>
            <div class="mensajes-lista" id="listaMensajes"></div>
        </div>

        <div id="password" class="tab-content">
            <h3>Cambiar contraseña</h3>
            <form id="formPassword" action="perfiles.php" method="post">
                <input type="hidden" name="action" value="change_password">
                <div class="input-group">
                    <label for="actual">Contraseña actual</label>
                    <input type="password" id="actual" name="actual" required>
                </div>
                <div class="input-group">
                    <label for="nueva">Nueva contraseña</label>
                    <input type="password" id="nueva" name="nueva" required>
                </div>
                <div class="input-group">
                    <label for="confirmar">Confirmar contraseña</label>
                    <input type="password" id="confirmar" name="confirmar" required>
                </div>
                <button type="submit" class="btn-guardar">Actualizar contraseña</button>
            </form>
        </div>
    </main>

    <?php include '../Fragmentos/footer.php'; ?>

    <script src="/sc502-ln-proyecto-grupo5-ln-2026/JS/header-footer.js"></script>
    <script src="/sc502-ln-proyecto-grupo5-ln-2026/JS/perfil.js"></script>
</body>
</html>