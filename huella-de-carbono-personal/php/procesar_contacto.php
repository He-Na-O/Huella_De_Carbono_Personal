<?php
// procesar_contacto.php - Versión corregida para tu tabla contacto
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Manejar preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Solo permitir POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
    exit();
}

// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "latidoverde"; // Cambia por el nombre de tu base de datos

try {
    // Conectar a la base de datos
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Error de conexión: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");

    // Obtener datos del formulario
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');

    // Validaciones
    $errores = [];

    if (empty($nombre)) {
        $errores[] = "El nombre es obligatorio";
    } elseif (strlen($nombre) < 2) {
        $errores[] = "El nombre debe tener al menos 2 caracteres";
    }

    if (empty($correo)) {
        $errores[] = "El correo es obligatorio";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo no es válido";
    }

    if (empty($mensaje)) {
        $errores[] = "El mensaje es obligatorio";
    } elseif (strlen($mensaje) < 10) {
        $errores[] = "El mensaje debe tener al menos 10 caracteres";
    }

    if (!empty($errores)) {
        echo json_encode([
            'success' => false,
            'message' => implode('. ', $errores)
        ]);
        exit;
    }

    // Insertar en la tabla contacto
    // ID_Usuario se deja como NULL porque es un formulario público
    $sql = "INSERT INTO contacto (ID_Usuario, Nombre, Email, Mensaje) VALUES (NULL, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Error preparando consulta: " . $conn->error);
    }
    
    $stmt->bind_param("sss", $nombre, $correo, $mensaje);
    
    if ($stmt->execute()) {
        $contacto_id = $conn->insert_id;
        
        echo json_encode([
            'success' => true,
            'message' => '¡Mensaje enviado correctamente! Te responderemos pronto.',
            'data' => [
                'id' => $contacto_id,
                'nombre' => $nombre,
                'correo' => $correo
            ]
        ]);
    } else {
        throw new Exception("Error al ejecutar consulta: " . $stmt->error);
    }
    
    $stmt->close();
    $conn->close();

} catch(Exception $e) {
    error_log("Error en procesar_contacto.php: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al procesar el mensaje: ' . $e->getMessage()
    ]);
}
?>