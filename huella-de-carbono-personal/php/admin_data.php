<?php
// admin_data.php - API completa para el panel de administraciÃ³n
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'conexion.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $pdo = getConnection();
    
    if (!$pdo) {
        throw new Exception('Error de conexiÃ³n a la base de datos');
    }
    
    $accion = isset($_GET['accion']) ? $_GET['accion'] : '';
    
    switch($accion) {
        case 'dashboard':
            obtenerDashboard($pdo);
            break;
            
        case 'usuarios':
            obtenerUsuarios($pdo);
            break;
            
        case 'contactos':
            obtenerContactos($pdo);
            break;
            
        case 'actividad':
            obtenerActividad($pdo);
            break;
            
        case 'resenas':
            obtenerResenas($pdo);
            break;
            
        case 'calculos':
            obtenerCalculos($pdo);
            break;
            
        default:
            throw new Exception('AcciÃ³n no vÃ¡lida');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'exito' => false,
        'mensaje' => $e->getMessage()
    ]);
}

// ========== FUNCIÃ“N DASHBOARD ==========
function obtenerDashboard($pdo) {
    // Total de usuarios
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuario");
    $totalUsuarios = $stmt->fetch()['total'];
    
    // Total de evaluaciones
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM evaluacion_huella");
    $totalEvaluaciones = $stmt->fetch()['total'];
    
    // Total de contactos
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM contacto");
    $totalContactos = $stmt->fetch()['total'];
    
    // Total de reseÃ±as
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM sistema_resenas");
    $totalResenas = $stmt->fetch()['total'];
    
    echo json_encode([
        'exito' => true,
        'datos' => [
            'usuarios' => $totalUsuarios,
            'evaluaciones' => $totalEvaluaciones,
            'resenas' => $totalResenas,
            'contactos' => $totalContactos
        ]
    ]);
}

// ========== FUNCIÃ“N USUARIOS ==========
function obtenerUsuarios($pdo) {
    $stmt = $pdo->query("
        SELECT 
            ID_Usuario as id,
            Nombre as nombre,
            Correo as email,
            Fecha_Registro as fecha,
            'Activo' as estado
        FROM usuario 
        ORDER BY Fecha_Registro DESC 
        LIMIT 100
    ");
    
    $usuarios = $stmt->fetchAll();
    
    echo json_encode([
        'exito' => true,
        'usuarios' => $usuarios
    ]);
}

// ========== FUNCIÃ“N CONTACTOS ==========
function obtenerContactos($pdo) {
    $stmt = $pdo->query("
        SELECT 
            ID_Contacto as id,
            Nombre as nombre,
            Email as email,
            SUBSTRING(Mensaje, 1, 50) as asunto,
            Mensaje as mensaje_completo,
            Fecha_Contacto as fecha,
            'Nuevo' as estado
        FROM contacto 
        ORDER BY Fecha_Contacto DESC 
        LIMIT 100
    ");
    
    $contactos = $stmt->fetchAll();
    
    echo json_encode([
        'exito' => true,
        'contactos' => $contactos
    ]);
}

// ========== FUNCIÃ“N ACTIVIDAD RECIENTE ==========
function obtenerActividad($pdo) {
    $actividades = [];
    
    // Ãšltimos usuarios registrados
    $stmt = $pdo->query("
        SELECT 
            Nombre as usuario,
            'Registro de usuario' as accion,
            Fecha_Registro as fecha,
            'Completado' as estado
        FROM usuario 
        ORDER BY Fecha_Registro DESC 
        LIMIT 5
    ");
    $actividades = array_merge($actividades, $stmt->fetchAll());
    
    // Ãšltimos contactos
    $stmt = $pdo->query("
        SELECT 
            Nombre as usuario,
            'Contacto enviado' as accion,
            Fecha_Contacto as fecha,
            'Nuevo' as estado
        FROM contacto 
        ORDER BY Fecha_Contacto DESC 
        LIMIT 5
    ");
    $actividades = array_merge($actividades, $stmt->fetchAll());
    
    // Ãšltimas evaluaciones
    $stmt = $pdo->query("
        SELECT 
            u.Nombre as usuario,
            'CÃ¡lculo de huella de carbono' as accion,
            e.Fecha as fecha,
            'Completado' as estado
        FROM evaluacion_huella e
        INNER JOIN usuario u ON e.ID_Usuario = u.ID_Usuario
        ORDER BY e.Fecha DESC 
        LIMIT 5
    ");
    $actividades = array_merge($actividades, $stmt->fetchAll());
    
    // Ordenar por fecha
    usort($actividades, function($a, $b) {
        return strtotime($b['fecha']) - strtotime($a['fecha']);
    });
    
    echo json_encode([
        'exito' => true,
        'actividades' => array_slice($actividades, 0, 10)
    ]);
}

// ========== FUNCIÃ“N RESEÃ‘AS ==========
function obtenerResenas($pdo) {
    $stmt = $pdo->query("
        SELECT 
            ID_Resena as id,
            nombre as usuario,
            contenido as comentario,
            calificacion,
            fecha,
            estado
        FROM sistema_resenas 
        ORDER BY fecha DESC 
        LIMIT 100
    ");
    
    $resenas = $stmt->fetchAll();
    
    echo json_encode([
        'exito' => true,
        'resenas' => $resenas
    ]);
}

// ========== FUNCIÃ“N CÃLCULOS ==========
function obtenerCalculos($pdo) {
    $stmt = $pdo->query("
        SELECT 
            e.ID_Evaluacion as id,
            u.Nombre as usuario,
            'Huella de Carbono' as tipo,
            CONCAT(e.Total, ' kg CO2') as resultado,
            e.Fecha as fecha
        FROM evaluacion_huella e
        INNER JOIN usuario u ON e.ID_Usuario = u.ID_Usuario
        ORDER BY e.Fecha DESC 
        LIMIT 100
    ");
    
    $calculos = $stmt->fetchAll();
    
    echo json_encode([
        'exito' => true,
        'calculos' => $calculos
    ]);
}
?>