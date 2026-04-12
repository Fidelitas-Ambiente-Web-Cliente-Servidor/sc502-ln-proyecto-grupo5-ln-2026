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
            if ($estadoStr === 'En Proceso') $estadoDB = 'en_proceso';
            elseif ($estadoStr === 'Resuelto') $estadoDB = 'resuelto';
            elseif ($estadoStr === 'Pendiente') $estadoDB = 'en_revision';
            else $estadoDB = strtolower(str_replace(' ', '_', $estadoStr)); // fallback

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
            $idUsuarioActual = $_SESSION['usuario_id'] ?? 1; // dummy si no hay sesion

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
            $tipoProblema = $conn->real_escape_string($input['tipoProblema'] ?? $_POST['tipoProblema'] ?? 'otro');
            $descripcion = $conn->real_escape_string($input['descripcion'] ?? $_POST['descripcion'] ?? '');
            $fecha = $conn->real_escape_string($input['fecha'] ?? $_POST['fecha'] ?? '');
            $hora = $conn->real_escape_string($input['hora'] ?? $_POST['hora'] ?? '');
            $gravedad = $conn->real_escape_string($input['gravedad'] ?? $_POST['gravedad'] ?? 'media');
            $lat = floatval($input['lat'] ?? $_POST['lat'] ?? 0);
            $lng = floatval($input['lng'] ?? $_POST['lng'] ?? 0);

            // Usuario por defecto si no hay sesion
            $usuario_id = $_SESSION['usuario_id'] ?? 1;

            // ---- AUTO POPULATE CATEGORIA SI NO EXISTE ---- 
            $catQuery = $conn->query("SELECT id FROM categorias WHERE nombre LIKE '%$tipoProblema%' LIMIT 1");
            if ($catQuery && $catQuery->num_rows > 0) {
                $categoria_id = $catQuery->fetch_assoc()['id'];
            } else {
                $conn->query("INSERT INTO categorias (nombre, descripcion) VALUES ('$tipoProblema', 'Categoría generada automáticamente')");
                $categoria_id = $conn->insert_id;
            }

            // ---- AUTO POPULATE ESTADO SI NO EXISTE ---- 
            $estQuery = $conn->query("SELECT id FROM estados WHERE nombre IN ('enviado', 'en_revision') LIMIT 1");
            if ($estQuery && $estQuery->num_rows > 0) {
                $estado_id = $estQuery->fetch_assoc()['id'];
            } else {
                $conn->query("INSERT INTO estados (nombre, descripcion, orden) VALUES ('enviado', 'Estado inicial autogenerado', 1)");
                $estado_id = $conn->insert_id;
            }

            $fechaHora = $fecha . ' ' . $hora . ':00'; // Formato DATETIME

            $sql = "INSERT INTO reportes (usuario_id, categoria_id, estado_id, descripcion, fecha_hora_incidente, gravedad, latitud, longitud) 
                    VALUES ($usuario_id, $categoria_id, $estado_id, '$descripcion', '$fechaHora', '$gravedad', $lat, $lng)";
            
            $res = $conn->query($sql);
            if (!$res) {
                throw new Exception("Error al guardar reporte en SQL: " . $conn->error);
            }
            $reporte_id = $conn->insert_id;

            // Procesado de imágenes adjuntas
            if (!empty($_FILES['imagenes']['name'][0])) {
                $uploadDir = '../img/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                foreach ($_FILES['imagenes']['name'] as $key => $name) {
                    if ($_FILES['imagenes']['error'][$key] == 0) {
                        $ext = pathinfo($name, PATHINFO_EXTENSION);
                        // Limpieza de extensión
                        $ext = strtolower($ext) ?: 'png';
                        $newName = "rep_" . $reporte_id . "_" . time() . "_" . $key . "." . $ext;
                        $target = $uploadDir . $newName;
                        if (move_uploaded_file($_FILES['imagenes']['tmp_name'][$key], $target)) {
                            // Guardamos URL relativa al host
                            $urlPath = '/sc502-ln-proyecto-grupo5-ln-2026/img/uploads/' . $newName;
                            $conn->query("INSERT INTO evidencias (reporte_id, url) VALUES ($reporte_id, '$urlPath')");
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
