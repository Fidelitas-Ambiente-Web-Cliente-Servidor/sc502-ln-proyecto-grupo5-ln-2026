<?php
session_start();
require_once '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $resultado = "Procesando login...<br>";
    $stmt = $conn->prepare("SELECT id, nombre, contraseña, tipo FROM usuarios WHERE email = ?");
    if (!$stmt) {
        $resultado .= "Error prepare: " . $conn->error;
    } else {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $resultado .= "Filas encontradas: " . $stmt->num_rows . "<br>";
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $nombre, $hashed, $tipo);
            $stmt->fetch();
            $resultado .= "Hash almacenado: $hashed<br>";
            if (password_verify($password, $hashed)) {
                $resultado .= "<strong style='color:green'>✅ LOGIN EXITOSO</strong><br>";
                $_SESSION['user_id'] = $id;
                $_SESSION['user_name'] = $nombre;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_type'] = $tipo;
                $resultado .= "Sesión guardada. ID de usuario: " . $_SESSION['user_id'];
            } else {
                $resultado .= "<strong style='color:red'>❌ Contraseña incorrecta</strong>";
            }
        } else {
            $resultado .= "<strong style='color:red'>❌ Usuario no encontrado</strong>";
        }
        $stmt->close();
    }
    // Mostrar el resultado sin redirigir
    echo $resultado;
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Login</title>
</head>
<body>
    <h2>Prueba de login (sin redirección)</h2>
    <form method="post">
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Contraseña" required><br>
        <button type="submit">Probar</button>
    </form>
</body>
</html>