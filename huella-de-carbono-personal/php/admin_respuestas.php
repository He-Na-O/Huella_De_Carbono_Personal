<?php
// admin_respuestas.php - API para gestionar respuestas del admin
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'conexion.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = getConnection();
    
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';
    
    switch($accion) {
        case 'responder_resena':
            responderResena($pdo);
            break;
            
        case 'obtener_respuestas':
            obtenerRespuestas($pdo);
            break;
            
        default:
            throw new Exception('Acción no válida');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'exito' => false,
        'mensaje' => $e->getMessage()
    ]);
}

function responderResena($pdo) {
    $idResena = isset($_POST['id_resena']) ? intval($_POST['id_resena']) : 0;
    $contenido = isset($_POST['contenido']) ? trim($_POST['contenido']) : '';
    $nombreAdmin = isset($_POST['nombre_admin']) ? trim($_POST['nombre_admin']) : 'Admin';
    
    if ($idResena <= 0 || empty($contenido)) {
        throw new Exception('Datos incompletos');
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO respuestas_admin 
        (ID_Resena, Contenido_Respuesta, Nombre_Admin, Fecha_Respuesta) 
        VALUES (?, ?, ?, NOW())
    ");
    
    $resultado = $stmt->execute([$idResena, $contenido, $nombreAdmin]);
    
    if ($resultado) {
        echo json_encode([
            'exito' => true,
            'mensaje' => 'Respuesta enviada correctamente',
            'id_respuesta' => $pdo->lastInsertId()
        ]);
    } else {
        throw new Exception('Error al guardar la respuesta');
    }
}

function obtenerRespuestas($pdo) {
    $idResena = isset($_GET['id_resena']) ? intval($_GET['id_resena']) : 0;
    
    if ($idResena > 0) {
        // Obtener respuestas de una reseña específica
        $stmt = $pdo->prepare("
            SELECT * FROM respuestas_admin 
            WHERE ID_Resena = ? 
            ORDER BY Fecha_Respuesta DESC
        ");
        $stmt->execute([$idResena]);
    } else {
        // Obtener todas las respuestas
        $stmt = $pdo->query("
            SELECT 
                ra.*,
                sr.nombre as nombre_resena,
                sr.contenido as contenido_resena
            FROM respuestas_admin ra
            INNER JOIN sistema_resenas sr ON ra.ID_Resena = sr.ID_Resena
            ORDER BY ra.Fecha_Respuesta DESC
            LIMIT 50
        ");
    }
    
    $respuestas = $stmt->fetchAll();
    
    echo json_encode([
        'exito' => true,
        'respuestas' => $respuestas
    ]);
}
?>