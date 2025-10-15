<?php
require_once 'conexion.php';

// Headers para JSON
header('Content-Type: application/json; charset=utf-8');

try {
    // Verificar que sea una peticiÃ³n POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('MÃ©todo no permitido');
    }

    // Obtener y validar datos
    $nombre = trim($_POST['nombre'] ?? '');
    $contenido = trim($_POST['contenido'] ?? '');
    $calificacion = intval($_POST['calificacion'] ?? 0);

    // Validaciones
    if (empty($nombre)) {
        throw new Exception('El nombre es obligatorio');
    }

    if (strlen($nombre) > 100) {
        throw new Exception('El nombre no puede exceder 100 caracteres');
    }

    if (empty($contenido)) {
        throw new Exception('El contenido de la reseÃ±a es obligatorio');
    }

    if (strlen($contenido) > 1000) {
        throw new Exception('La reseÃ±a no puede exceder 1000 caracteres');
    }

    if ($calificacion < 1 || $calificacion > 5) {
        throw new Exception('La calificaciÃ³n debe estar entre 1 y 5');
    }

    // Obtener conexiÃ³n usando tu funciÃ³n existente
    $pdo = getConnection();
    if (!$pdo) {
        throw new Exception('Error de conexiÃ³n a la base de datos');
    }

    // Insertar reseÃ±a
    $sql = "INSERT INTO sistema_resenas (nombre, contenido, calificacion) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    $resultado = $stmt->execute([
        $nombre,
        $contenido,
        $calificacion
    ]);

    if ($resultado) {
        echo json_encode([
            'status' => 'ok',
            'msg' => 'Â¡Gracias por tu reseÃ±a! Ha sido enviada exitosamente.',
            'id' => $pdo->lastInsertId()
        ], JSON_UNESCAPED_UNICODE);
    } else {
        throw new Exception('Error al guardar la reseÃ±a');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>