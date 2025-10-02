<?php
// archivo: /brainer/huella-de-carbono-personal/php/listar_historial.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    // Configuración de base de datos
    $host = 'localhost';
    $dbname = 'latidoverde';
    $username = 'root';
    $password = '';

    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Obtener historial de evaluaciones (últimas 10)
    $stmt = $pdo->query("SELECT * FROM evaluacion_huella ORDER BY Fecha DESC LIMIT 10");
    $historial = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($historial);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error de base de datos',
        'message' => $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error interno',
        'message' => $e->getMessage()
    ]);
}
?>