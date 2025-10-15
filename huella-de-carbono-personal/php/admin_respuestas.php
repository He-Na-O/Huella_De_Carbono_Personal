<?php
// admin_respuestas.php - API para listar todas las reseñas en el panel admin
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/conexion.php';

// Headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    // Obtener conexión
    $pdo = getConnection();
    
    if (!$pdo) {
        throw new Exception('Error de conexión a la base de datos');
    }
    
    // Consultar TODAS las reseñas (incluyendo inactivas para el admin)
    $sql = "SELECT 
                ID_Resena as id,
                nombre,
                contenido,
                calificacion,
                fecha,
                COALESCE(estado, 'activo') as estado
            FROM sistema_resenas 
            ORDER BY fecha DESC";
    
    $stmt = $pdo->query($sql);
    $resenas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // CRÍTICO: Manejar el caso cuando no hay reseñas
    if (empty($resenas)) {
        echo json_encode([
            'exito' => true,
            'data' => [],
            'mensaje' => 'No hay reseñas disponibles',
            'total' => 0
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // Retornar reseñas encontradas
    echo json_encode([
        'exito' => true,
        'data' => $resenas,
        'total' => count($resenas)
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    error_log("Error en admin_respuestas.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'exito' => false,
        'data' => [],
        'mensaje' => 'Error al cargar las reseñas: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>