<?php
// admin_acciones_usuarios.php - API para gestionar usuarios
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir el archivo de conexión
require_once __DIR__ . '/conexion.php';

// Headers CORS y JSON
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Responder a peticiones OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Log de la petición para debugging
error_log("========================================");
error_log("Nueva petición a admin_acciones_usuarios.php");
error_log("Método: " . $_SERVER['REQUEST_METHOD']);
error_log("GET: " . json_encode($_GET));
error_log("POST: " . json_encode($_POST));
error_log("========================================");

try {
    // Obtener conexión
    $pdo = getConnection();
    
    if (!$pdo) {
        throw new Exception('No se pudo establecer conexión con la base de datos');
    }
    
    // Determinar la acción
    $accion = '';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $accion = isset($_POST['accion']) ? $_POST['accion'] : '';
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $accion = isset($_GET['accion']) ? $_GET['accion'] : '';
    }
    
    error_log("Acción detectada: " . $accion);
    
    // Ejecutar la acción correspondiente
    switch($accion) {
        case 'editar':
            editarUsuario($pdo);
            break;
            
        case 'eliminar':
            eliminarUsuario($pdo);
            break;
            
        case 'obtener_uno':
            obtenerUnUsuario($pdo);
            break;
            
        case '':
            throw new Exception('No se especificó ninguna acción');
            
        default:
            throw new Exception('Acción no válida: ' . $accion);
    }
    
} catch (Exception $e) {
    error_log("ERROR: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'exito' => false,
        'mensaje' => $e->getMessage(),
        'detalles' => $e->getTraceAsString()
    ]);
}

// ========== FUNCIÓN EDITAR USUARIO ==========
function editarUsuario($pdo) {
    try {
        error_log("Iniciando edición de usuario");
        
        // Obtener y validar datos
        $idUsuario = isset($_POST['id_usuario']) ? intval($_POST['id_usuario']) : 0;
        $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
        $correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
        $edad = isset($_POST['edad']) ? intval($_POST['edad']) : 0;
        
        error_log("Datos recibidos - ID: $idUsuario, Nombre: $nombre, Correo: $correo, Edad: $edad");
        
        // Validaciones
        if ($idUsuario <= 0) {
            throw new Exception('ID de usuario inválido');
        }
        
        if (empty($nombre) || strlen($nombre) > 100) {
            throw new Exception('El nombre debe tener entre 1 y 100 caracteres');
        }
        
        if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('El correo electrónico no es válido');
        }
        
        if ($edad < 13 || $edad > 120) {
            throw new Exception('La edad debe estar entre 13 y 120 años');
        }
        
        // Verificar que el usuario existe
        $stmt = $pdo->prepare("SELECT ID_Usuario FROM usuario WHERE ID_Usuario = ?");
        $stmt->execute([$idUsuario]);
        
        if (!$stmt->fetch()) {
            throw new Exception('El usuario con ID ' . $idUsuario . ' no existe en la base de datos');
        }
        
        // Verificar que el correo no esté siendo usado por otro usuario
        $stmt = $pdo->prepare("SELECT ID_Usuario FROM usuario WHERE Correo = ? AND ID_Usuario != ?");
        $stmt->execute([$correo, $idUsuario]);
        
        if ($stmt->fetch()) {
            throw new Exception('El correo electrónico ya está siendo usado por otro usuario');
        }
        
        // Actualizar el usuario
        $sql = "UPDATE usuario 
                SET Nombre = ?, 
                    Correo = ?, 
                    Edad = ?
                WHERE ID_Usuario = ?";
        
        $stmt = $pdo->prepare($sql);
        $resultado = $stmt->execute([
            $nombre,
            $correo,
            $edad,
            $idUsuario
        ]);
        
        if ($resultado) {
            error_log("Usuario actualizado exitosamente - ID: $idUsuario");
            echo json_encode([
                'exito' => true,
                'mensaje' => 'Usuario actualizado correctamente en la base de datos'
            ]);
        } else {
            throw new Exception('Error al ejecutar la actualización en la base de datos');
        }
        
    } catch (Exception $e) {
        error_log("Error en editarUsuario: " . $e->getMessage());
        throw $e;
    }
}

// ========== FUNCIÓN ELIMINAR USUARIO ==========
function eliminarUsuario($pdo) {
    try {
        error_log("Iniciando eliminación de usuario");
        
        $idUsuario = isset($_POST['id_usuario']) ? intval($_POST['id_usuario']) : 0;
        
        error_log("ID a eliminar: $idUsuario");
        
        if ($idUsuario <= 0) {
            throw new Exception('ID de usuario inválido');
        }
        
        // Verificar que el usuario existe
        $stmt = $pdo->prepare("SELECT ID_Usuario, Nombre, Correo FROM usuario WHERE ID_Usuario = ?");
        $stmt->execute([$idUsuario]);
        $usuario = $stmt->fetch();
        
        if (!$usuario) {
            throw new Exception('El usuario con ID ' . $idUsuario . ' no existe en la base de datos');
        }
        
        error_log("Usuario encontrado: " . $usuario['Nombre'] . " (" . $usuario['Correo'] . ")");
        
        // IMPORTANTE: Primero eliminar registros relacionados para evitar errores de foreign key
        
        // Eliminar evaluaciones de huella del usuario
        $stmt = $pdo->prepare("DELETE FROM evaluacion_huella WHERE ID_Usuario = ?");
        $stmt->execute([$idUsuario]);
        $evaluacionesEliminadas = $stmt->rowCount();
        error_log("Evaluaciones eliminadas: $evaluacionesEliminadas");
        
        // Ahora eliminar el usuario (DELETE permanente)
        $stmt = $pdo->prepare("DELETE FROM usuario WHERE ID_Usuario = ?");
        $resultado = $stmt->execute([$idUsuario]);
        
        if ($resultado && $stmt->rowCount() > 0) {
            error_log("Usuario eliminado exitosamente de la base de datos - ID: $idUsuario");
            echo json_encode([
                'exito' => true,
                'mensaje' => 'Usuario eliminado permanentemente de la base de datos',
                'id_eliminado' => $idUsuario,
                'registros_relacionados_eliminados' => $evaluacionesEliminadas
            ]);
        } else {
            throw new Exception('No se pudo eliminar el usuario de la base de datos');
        }
        
    } catch (Exception $e) {
        error_log("Error en eliminarUsuario: " . $e->getMessage());
        throw $e;
    }
}

// ========== FUNCIÓN OBTENER UN USUARIO ==========
function obtenerUnUsuario($pdo) {
    try {
        error_log("Obteniendo un usuario");
        
        $idUsuario = isset($_GET['id_usuario']) ? intval($_GET['id_usuario']) : 0;
        
        error_log("ID solicitado: $idUsuario");
        
        if ($idUsuario <= 0) {
            throw new Exception('ID de usuario inválido');
        }
        
        $stmt = $pdo->prepare("
            SELECT 
                ID_Usuario as id,
                Nombre as nombre,
                Correo as correo,
                Edad as edad,
                Fecha_Registro as fecha_registro
            FROM usuario 
            WHERE ID_Usuario = ?
        ");
        
        $stmt->execute([$idUsuario]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$usuario) {
            throw new Exception('Usuario no encontrado con ID: ' . $idUsuario);
        }
        
        error_log("Usuario encontrado: " . json_encode($usuario));
        
        echo json_encode([
            'exito' => true,
            'usuario' => $usuario
        ]);
        
    } catch (Exception $e) {
        error_log("Error en obtenerUnUsuario: " . $e->getMessage());
        throw $e;
    }
}
?>