<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'conexion.php';

try {
    $pdo = getConnection();
    
    // Obtener datos del POST
    $id_usuario = isset($_POST['id_usuario']) ? intval($_POST['id_usuario']) : null;
    $transporte = isset($_POST['transporte']) ? floatval($_POST['transporte']) : 0;
    $energia = isset($_POST['energia']) ? floatval($_POST['energia']) : 0;
    $alimentacion = isset($_POST['alimentacion']) ? floatval($_POST['alimentacion']) : 0;
    $total = isset($_POST['total']) ? floatval($_POST['total']) : 0;
    
    // VERIFICAR SI EL USUARIO EXISTE O CREAR UNO TEMPORAL
    if ($id_usuario) {
        $stmt = $pdo->prepare("SELECT ID_Usuario FROM usuario WHERE ID_Usuario = ?");
        $stmt->execute([$id_usuario]);
        $usuario_existe = $stmt->fetch();
        
        if (!$usuario_existe) {
            $id_usuario = null;
        }
    }
    
    // Si no hay usuario válido, buscar el primero disponible o crear uno
    if (!$id_usuario) {
        $stmt = $pdo->query("SELECT ID_Usuario FROM usuario LIMIT 1");
        $primer_usuario = $stmt->fetch();
        
        if ($primer_usuario) {
            $id_usuario = $primer_usuario['ID_Usuario'];
        } else {
            // Crear usuario temporal si no existe ninguno
            $stmt = $pdo->prepare("INSERT INTO usuario (Nombre, Correo, Contrasena, Edad, Fecha_Registro) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute(['Usuario Temporal', 'temp@latidoverde.com', 'temp123', 25]);
            $id_usuario = $pdo->lastInsertId();
        }
    }
    
    // Insertar el cálculo
    $sql = "INSERT INTO evaluacion_huella (ID_Usuario, Transporte, Energia, Alimentacion, Total, Fecha) 
            VALUES (:id_usuario, :transporte, :energia, :alimentacion, :total, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->bindParam(':transporte', $transporte);
    $stmt->bindParam(':energia', $energia);
    $stmt->bindParam(':alimentacion', $alimentacion);
    $stmt->bindParam(':total', $total);
    
    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success', 
            'message' => 'Cálculo guardado exitosamente',
            'id' => $pdo->lastInsertId(),
            'id_usuario' => $id_usuario
        ]);
    } else {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Error al ejecutar la consulta'
        ]);
    }
    
} catch(PDOException $e) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Error de base de datos: ' . $e->getMessage()
    ]);
} catch(Exception $e) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>