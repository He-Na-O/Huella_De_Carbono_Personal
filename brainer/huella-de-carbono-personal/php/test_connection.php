<?php
require_once 'database.php';

header('Content-Type: application/json');

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    echo json_encode([
        'success' => true,
        'message' => '✅ Conexión exitosa a la base de datos',
        'database' => 'latidoverde',
        'table_exists' => $database->tableExists('usuario'),
        'connection_info' => $database->getConnectionInfo()
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => '❌ Error: ' . $e->getMessage()
    ]);
}
?>