<?php
// adminAcciones.php
session_start();
header('Content-Type: application/json');

include '../conexion.php';

$input = json_decode(file_get_contents('php://input'), true);
$accion = $input['accion'] ?? $_POST['accion'] ?? '';

$response = ['success' => false, 'message' => 'Acción no válida'];

try {
    switch ($accion) {
        case 'estado-usuario':
            $id = intval($input['id']);
            $conn->query("UPDATE usuarios SET estado = IF(estado='activo', 'inactivo', 'activo') WHERE id = $id");
            $response = ['success' => true];
            break;

        case 'rol-usuario':
            $id = intval($input['id']);
            $conn->query("UPDATE usuarios SET tipo = IF(tipo='usuario', 'admin', 'usuario') WHERE id = $id");
            $response = ['success' => true];
            break;

        case 'eliminar-usuario':
            $id = intval($input['id']);
            $conn->query("DELETE FROM usuarios WHERE id = $id");
            $response = ['success' => true];
            break;

        case 'estado-reporte':
            $id = intval($input['id']);
            $estadoStr = $conn->real_escape_string($input['estado']); // Ej: 'enviado', 'en_proceso'

            // Buscamos el ID del estado con base al nombre "similar" para simplificar o buscar por equivalencia
            // Para mantener simpleza, si la vista manda 'Pendiente', lo pasamos a un estado de la DB
            $estadoDB = '';
            if ($estadoStr === 'En Proceso')
                $estadoDB = 'en_proceso';
            elseif ($estadoStr === 'Resuelto')
                $estadoDB = 'resuelto';
            elseif ($estadoStr === 'Pendiente')
                $estadoDB = 'en_revision';
            else
                $estadoDB = strtolower(str_replace(' ', '_', $estadoStr)); // fallback

            $qEstado = $conn->query("SELECT id FROM estados WHERE nombre = '$estadoDB' LIMIT 1");
            if ($qEstado && $qEstado->num_rows > 0) {
                $eid = $qEstado->fetch_assoc()['id'];
            } else {
                $conn->query("INSERT INTO estados (nombre, descripcion, orden) VALUES ('$estadoDB', 'Estado autogenerado', 2)");
                $eid = $conn->insert_id;
            }
            $conn->query("UPDATE reportes SET estado_id = $eid WHERE id = $id");
            $response = ['success' => true];
            break;

        case 'eliminar-reporte':
            $id = intval($input['id']);
            $conn->query("DELETE FROM evidencias WHERE reporte_id = $id"); // por precaución
            $conn->query("DELETE FROM seguimiento_estados WHERE reporte_id = $id");
            $conn->query("DELETE FROM reportes WHERE id = $id");
            $response = ['success' => true];
            break;

        case 'asignar-institucion':
            $id = intval($input['id_reporte']);
            $usuario_id = intval($input['id_usuario']); // En el diseño usan "usuario", pero la BD tiene instituciones para asignar
            // Para mantener compatibilidad si usan usuarios de institucion, lo mapearemos a institucion_id o simularemos.
            // Utilizaremos institucion_id de una institución genérica o dejaremos el id en la tabla.
            $conn->query("UPDATE reportes SET institucion_id = $usuario_id WHERE id = $id");
            $response = ['success' => true];
            break;

        case 'agregar-comentario':
            $id = intval($input['id_reporte']);
            $texto = $conn->real_escape_string($input['texto']);

            // Asumiendo que tenemos un usuario logueado en la sesion
            $idUsuarioActual = $_SESSION['user_id'] ?? 1; // dummy si no hay sesion

            // Agregamos seguimiento
            // Buscamos el estado actual
            $qActual = $conn->query("SELECT estado_id FROM reportes WHERE id = $id");
            $est_id = $qActual->fetch_assoc()['estado_id'] ?? 1;

            $conn->query("INSERT INTO seguimiento_estados (reporte_id, estado_id, usuario_id, comentario) 
                          VALUES ($id, $est_id, $idUsuarioActual, '$texto')");
            $response = ['success' => true];
            break;

        case 'cerrar-caso':
            $id = intval($input['id_reporte']);

            $qEstado = $conn->query("SELECT id FROM estados WHERE nombre = 'cerrado' LIMIT 1");
            if ($qEstado && $qEstado->num_rows > 0) {
                $eid = $qEstado->fetch_assoc()['id'];
            } else {
                $conn->query("INSERT INTO estados (nombre, descripcion, orden) VALUES ('cerrado', 'Caso cerrado', 99)");
                $eid = $conn->insert_id;
            }

            $conn->query("UPDATE reportes SET estado_id = $eid, fecha_cierre = NOW() WHERE id = $id");
            $response = ['success' => true];
            break;

        case 'crear-reporte':
    // Validar sesión
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Debes iniciar sesión para crear un reporte.');
    }
    $usuario_id = (int)$_SESSION['user_id'];

    // Validar campos requeridos
    $campos = ['tipoProblema', 'descripcion', 'fecha', 'hora', 'gravedad', 'lat', 'lng'];
    foreach ($campos as $campo) {
        if (empty($_POST[$campo])) {
            throw new Exception("El campo $campo es obligatorio.");
        }
    }

    $tipoProblema = trim($_POST['tipoProblema']);
    $descripcion = trim($_POST['descripcion']);
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $gravedad_raw = trim($_POST['gravedad']);
    $lat = (float)$_POST['lat'];
    $lng = (float)$_POST['lng'];
    $fechaHora = "$fecha $hora:00";

    // Validar gravedad (case-insensitive)
    $allowed = ['baja', 'media', 'alta', 'critica'];
    $gravedad = strtolower($gravedad_raw);
    if (!in_array($gravedad, $allowed)) {
        throw new Exception("Valor de gravedad no válido: '$gravedad_raw'. Debe ser: baja, media, alta, critica");
    }

    // --- Categoría ---
    $stmt = $conn->prepare("SELECT id FROM categorias WHERE LOWER(nombre) = LOWER(?)");
    $stmt->bind_param("s", $tipoProblema);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $categoria_id = $res->fetch_assoc()['id'];
    } else {
        $stmt2 = $conn->prepare("INSERT INTO categorias (nombre) VALUES (?)");
        $stmt2->bind_param("s", $tipoProblema);
        $stmt2->execute();
        $categoria_id = $conn->insert_id;
        $stmt2->close();
    }
    $stmt->close();

    // --- Estado inicial 'enviado' ---
    $stmt = $conn->prepare("SELECT id FROM estados WHERE nombre = 'enviado' LIMIT 1");
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows === 0) {
        $stmt2 = $conn->prepare("INSERT INTO estados (nombre, descripcion, orden) VALUES ('enviado', 'Reporte enviado por usuario', 1)");
        $stmt2->execute();
        $estado_id = $conn->insert_id;
        $stmt2->close();
    } else {
        $estado_id = $res->fetch_assoc()['id'];
    }
    $stmt->close();

    // --- Insertar reporte ---
    $sql = "INSERT INTO reportes (usuario_id, categoria_id, estado_id, descripcion, fecha_hora_incidente, gravedad, latitud, longitud) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    // Tipos: i=entero, s=string, d=double (decimal)
    $stmt->bind_param("iiisssdd", $usuario_id, $categoria_id, $estado_id, $descripcion, $fechaHora, $gravedad, $lat, $lng);
    if (!$stmt->execute()) {
        throw new Exception("Error al guardar reporte: " . $stmt->error);
    }
    $reporte_id = $conn->insert_id;  // <--- IMPORTANTE: asignar el ID generado
    $stmt->close();

    // --- Subir imágenes (si existen) ---
    if (isset($_FILES['imagenes']) && is_array($_FILES['imagenes']['name']) && !empty($_FILES['imagenes']['name'][0])) {
        $dir = __DIR__ . '/../img/uploads/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $total_archivos = count($_FILES['imagenes']['name']);
        for ($i = 0; $i < $total_archivos; $i++) {
            if ($_FILES['imagenes']['error'][$i] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['imagenes']['name'][$i], PATHINFO_EXTENSION);
                $nombre_archivo = "rep_{$reporte_id}_{$i}." . $ext;
                $ruta_destino = $dir . $nombre_archivo;
                if (move_uploaded_file($_FILES['imagenes']['tmp_name'][$i], $ruta_destino)) {
                    $url = "/sc502-ln-proyecto-grupo5-ln-2026/img/uploads/$nombre_archivo";
                    $stmt_img = $conn->prepare("INSERT INTO evidencias (reporte_id, url, tipo) VALUES (?, ?, 'imagen')");
                    $stmt_img->bind_param("is", $reporte_id, $url);
                    $stmt_img->execute();
                    $stmt_img->close();
                } else {
                    // Opcional: registrar error de movimiento, pero no detenemos el proceso
                    error_log("No se pudo mover la imagen $i para el reporte $reporte_id");
                }
            }
        }
    }

    $response = ['success' => true, 'reporte_id' => $reporte_id];
    break;
        case 'marcar-leido-mensaje':
            $id = intval($input['id']);
            $conn->query("UPDATE mensajes SET leido = 1 WHERE id = $id");
            $response = ['success' => true];
            break;

        case 'eliminar-mensaje':
            $id = intval($input['id']);
            $conn->query("UPDATE mensajes SET estado = 'inactivo' WHERE id = $id");
            $response = ['success' => true];
            break;
    }
} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

echo json_encode($response);
exit;
