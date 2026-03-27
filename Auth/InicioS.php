<?php
session_start();
require_once '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $errors = [];
    if (empty($email) || empty($password)) {
        $errors[] = "Todos los campos son obligatorios";
    } else {
        $stmt = $conn->prepare("SELECT id, nombre, contraseña, tipo FROM usuarios WHERE email = ?");
        if (!$stmt) {
            $errors[] = "Error en la consulta: " . $conn->error;
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows === 1) {
                $stmt->bind_result($id, $nombre, $hashed, $tipo);
                $stmt->fetch();
                if (password_verify($password, $hashed)) {
                    // Login exitoso
                    $_SESSION['user_id'] = $id;
                    $_SESSION['user_name'] = $nombre;
                    $_SESSION['user_email'] = $email;
                    $_SESSION['user_type'] = $tipo;

                    if (isset($_POST['recordar'])) {
                        ini_set('session.cookie_lifetime', 30 * 24 * 60 * 60);
                    }
                    file_put_contents(__DIR__ . '/session_debug.log', print_r($_SESSION, true));
                    echo '<!DOCTYPE html>
                    <html>
                    <head>
                        <meta charset="UTF-8">
                        <title>Redirigiendo...</title>
                        <script>
                            window.location.href = "/sc502-ln-proyecto-grupo5-ln-2026/Index.php";
                        </script>
                    </head>
                    <body>
                        <p>Redirigiendo a la página principal...</p>
                    </body>
                    </html>';
                    exit;
                } else {
                    $errors[] = "Credenciales incorrectas";
                }
            } else {
                $errors[] = "Credenciales incorrectas";
            }
            $stmt->close();
        }
    }

    $_SESSION['login_errors'] = $errors;
    $_SESSION['login_email'] = $email;
    header("Location: InicioS.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión | EcoAlerta CR</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="../CSS/styleIniciarS.css">
    <link rel="stylesheet" href="../CSS/HeaderStyle.css">
    <link rel="stylesheet" href="../CSS/styleFooter.css">
</head>
<body class="page-login">
    <?php include '../Fragmentos/header.php'; ?>

    <main class="login-container">
        <div class="login-card">
            <h2>Iniciar Sesión</h2>

            <?php
            if (isset($_SESSION['login_errors']) && count($_SESSION['login_errors']) > 0) {
                echo '<div class="error-messages" style="background:#ffdddd; border:1px solid #ff0000; padding:10px; margin-bottom:20px;"><ul>';
                foreach ($_SESSION['login_errors'] as $err) {
                    echo "<li style='color:red;'>$err</li>";
                }
                echo '</ul></div>';
                unset($_SESSION['login_errors']);
            }
            $email = isset($_SESSION['login_email']) ? $_SESSION['login_email'] : '';
            unset($_SESSION['login_email']);
            ?>

            <form action="InicioS.php" method="POST" id="loginForm">
                <div class="input-group">
                    <label for="email">Correo electrónico</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                <div class="input-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="opciones">
                    <label>
                        <input type="checkbox" name="recordar">
                        Recordarme
                    </label>
                    <a href="#">¿Olvidaste tu contraseña?</a>
                </div>
                <button type="submit" class="btn-login">Iniciar Sesión</button>
            </form>
            <p class="registro-link">
                ¿No tienes cuenta?
                <a href="Registro.php">Crear cuenta</a>
            </p>
        </div>
    </main>

    <?php include '../Fragmentos/footer.php'; ?>

    <script src="/sc502-ln-proyecto-grupo5-ln-2026/JS/header-footer.js"></script>
    <script src="/JS/ingreso.js"></script>
</body>
</html>