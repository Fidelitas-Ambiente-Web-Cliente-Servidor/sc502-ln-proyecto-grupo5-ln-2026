<?php
session_start();
require_once '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $password = $_POST['contrasenna'];

    // Validaciones
    $errors = [];
    if (empty($nombre)) $errors[] = "Nombre es obligatorio";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email inválido";
    if (empty($telefono) || !preg_match('/^\d+$/', $telefono) || strlen($telefono) < 8) $errors[] = "Teléfono inválido (mínimo 8 dígitos)";
    if (empty($password) || strlen($password) < 6) $errors[] = "Contraseña debe tener al menos 6 caracteres";

    if (empty($errors)) {
        $check = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) $errors[] = "El correo ya está registrado";
        $check->close();
    }

    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, telefono, contraseña, tipo) VALUES (?, ?, ?, ?, 'usuario')");
        $stmt->bind_param("ssss", $nombre, $email, $telefono, $hashed);
        if ($stmt->execute()) {
            header("Location: InicioS.php?registro=exito");
            exit;
        } else {
            $errors[] = "Error al registrar: " . $conn->error;
        }
        $stmt->close();
    }

    $_SESSION['registro_errors'] = $errors;
    $_SESSION['registro_data'] = $_POST;
    header("Location: Registro.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Registro | EcoAlerta CR</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="../CSS/HeaderStyle.css">
    <link rel="stylesheet" href="../CSS/styleRegistro1.css">
    <link rel="stylesheet" href="../CSS/styleFooter.css">
</head>
<body class="page-registro">
    <?php include '../Fragmentos/header.php'; ?>

    <main class="registro-container">
        <div class="registro-card">
            <h2>Registrarse</h2>
            <?php
            if (isset($_SESSION['registro_errors']) && count($_SESSION['registro_errors']) > 0) {
                echo '<div class="error-messages"><ul>';
                foreach ($_SESSION['registro_errors'] as $err) {
                    echo "<li>$err</li>";
                }
                echo '</ul></div>';
                unset($_SESSION['registro_errors']);
            }
            $data = isset($_SESSION['registro_data']) ? $_SESSION['registro_data'] : [];
            unset($_SESSION['registro_data']);
            ?>
            <form action="Registro.php" method="post" id="registroForm">
                <div class="input-group">
                    <label for="nombreR">Nombre Completo</label>
                    <input type="text" id="nombreR" name="nombre" value="<?php echo htmlspecialchars($data['nombre'] ?? ''); ?>">
                </div>
                <div class="input-group">
                    <label for="emailR">Correo</label>
                    <input type="email" id="emailR" name="email" value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>">
                </div>
                <div class="input-group">
                    <label for="telR">Teléfono</label>
                    <input type="tel" id="telR" name="telefono" value="<?php echo htmlspecialchars($data['telefono'] ?? ''); ?>">
                </div>
                <div class="input-group">
                    <label for="contraR">Contraseña</label>
                    <input type="password" id="contraR" name="contrasenna">
                </div>

                <div class="terminos">
                    <label>
                        <input type="checkbox" name="terminos" value="1">
                        Aceptar Términos y Condiciones
                    </label>
                </div>

                <button type="submit" class="btn-registro">
                    Registrarse
                </button>
            </form>
            <div class="inicio-link">
                ¿Ya tienes Cuenta?
                <a href="Login.php">Iniciar Sesión</a>
            </div>
        </div>
    </main>

    <?php include '../Fragmentos/footer.php'; ?>

    <script src="/sc502-ln-proyecto-grupo5-ln-2026/JS/header-footer.js"></script>
    <script src="/sc502-ln-proyecto-grupo5-ln-2026/JS/validarRegistro.js"></script>
</body>
</html>