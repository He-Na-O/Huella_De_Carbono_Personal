<?php
// admin_acciones_resenas.php - API para gestionar reseñas
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
error_log("Nueva petición a admin_acciones_resenas.php");
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
            editarResena($pdo);
            break;
            
        case 'eliminar':
            eliminarResena($pdo);
            break;
            
        case 'obtener_una':
            obtenerUnaResena($pdo);
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

// ========== FUNCIÓN EDITAR RESEÑA ==========
function editarResena($pdo) {
    try {
        error_log("Iniciando edición de reseña");
        
        // Obtener y validar datos
        $idResena = isset($_POST['id_resena']) ? intval($_POST['id_resena']) : 0;
        $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
        $contenido = isset($_POST['contenido']) ? trim($_POST['contenido']) : '';
        $calificacion = isset($_POST['calificacion']) ? intval($_POST['calificacion']) : 0;
        $estado = isset($_POST['estado']) ? trim($_POST['estado']) : 'activo';
        
        error_log("Datos recibidos - ID: $idResena, Nombre: $nombre, Calificación: $calificacion, Estado: $estado");
        
        // Validaciones
        if ($idResena <= 0) {
            throw new Exception('ID de reseña inválido');
        }
        
        if (empty($nombre) || strlen($nombre) > 100) {
            throw new Exception('El nombre debe tener entre 1 y 100 caracteres');
        }
        
        if (empty($contenido) || strlen($contenido) > 1000) {
            throw new Exception('El contenido debe tener entre 1 y 1000 caracteres');
        }
        
        if ($calificacion < 1 || $calificacion > 5) {
            throw new Exception('La calificación debe estar entre 1 y 5');
        }
        
        if (!in_array($estado, ['activo', 'inactivo', 'pendiente'])) {
            throw new Exception('Estado no válido. Debe ser: activo, inactivo o pendiente');
        }
        
        // Verificar que la reseña existe
        $stmt = $pdo->prepare("SELECT ID_Resena FROM sistema_resenas WHERE ID_Resena = ?");
        $stmt->execute([$idResena]);
        
        if (!$stmt->fetch()) {
            throw new Exception('La reseña con ID ' . $idResena . ' no existe en la base de datos');
        }
        
        // Actualizar la reseña
        $sql = "UPDATE sistema_resenas 
                SET nombre = ?, 
                    contenido = ?, 
                    calificacion = ?,
                    estado = ?
                WHERE ID_Resena = ?";
        
        $stmt = $pdo->prepare($sql);
        $resultado = $stmt->execute([
            $nombre,
            $contenido,
            $calificacion,
            $estado,
            $idResena
        ]);
        
        if ($resultado) {
            error_log("Reseña actualizada exitosamente - ID: $idResena");
            echo json_encode([
                'exito' => true,
                'mensaje' => 'Reseña actualizada correctamente en la base de datos'
            ]);
        } else {
            throw new Exception('Error al ejecutar la actualización en la base de datos');
        }
        
    } catch (Exception $e) {
        error_log("Error en editarResena: " . $e->getMessage());
        throw $e;
    }
}

// ========== FUNCIÓN ELIMINAR RESEÑA ==========
function eliminarResena($pdo) {
    try {
        error_log("Iniciando eliminación de reseña");
        
        $idResena = isset($_POST['id_resena']) ? intval($_POST['id_resena']) : 0;
        
        error_log("ID a eliminar: $idResena");
        
        if ($idResena <= 0) {
            throw new Exception('ID de reseña inválido');
        }
        
        // Verificar que la reseña existe
        $stmt = $pdo->prepare("SELECT ID_Resena, nombre FROM sistema_resenas WHERE ID_Resena = ?");
        $stmt->execute([$idResena]);
        $resena = $stmt->fetch();
        
        if (!$resena) {
            throw new Exception('La reseña con ID ' . $idResena . ' no existe en la base de datos');
        }
        
        error_log("Reseña encontrada: " . $resena['nombre']);
        
        // Eliminar la reseña (DELETE permanente)
        $stmt = $pdo->prepare("DELETE FROM sistema_resenas WHERE ID_Resena = ?");
        $resultado = $stmt->execute([$idResena]);
        
        if ($resultado && $stmt->rowCount() > 0) {
            error_log("Reseña eliminada exitosamente de la base de datos - ID: $idResena");
            echo json_encode([
                'exito' => true,
                'mensaje' => 'Reseña eliminada permanentemente de la base de datos',
                'id_eliminado' => $idResena
            ]);
        } else {
            throw new Exception('No se pudo eliminar la reseña de la base de datos');
        }
        
    } catch (Exception $e) {
        error_log("Error en eliminarResena: " . $e->getMessage());
        throw $e;
    }
}

// ========== FUNCIÓN OBTENER UNA RESEÑA ==========
function obtenerUnaResena($pdo) {
    try {
        error_log("Obteniendo una reseña");
        
        $idResena = isset($_GET['id_resena']) ? intval($_GET['id_resena']) : 0;
        
        error_log("ID solicitado: $idResena");
        
        if ($idResena <= 0) {
            throw new Exception('ID de reseña inválido');
        }
        
        $stmt = $pdo->prepare("
            SELECT 
                ID_Resena as id,
                nombre,
                contenido,
                calificacion,
                fecha,
                COALESCE(estado, 'activo') as estado
            FROM sistema_resenas 
            WHERE ID_Resena = ?
        ");
        
        $stmt->execute([$idResena]);
        $resena = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$resena) {
            throw new Exception('Reseña no encontrada con ID: ' . $idResena);
        }
        
        error_log("Reseña encontrada: " . json_encode($resena));
        
        echo json_encode([
            'exito' => true,
            'resena' => $resena
        ]);
        
    } catch (Exception $e) {
        error_log("Error en obtenerUnaResena: " . $e->getMessage());
        throw $e;
    }
}
?>