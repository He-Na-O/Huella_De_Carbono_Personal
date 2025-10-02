<?php
// conexion.php - Configuración de conexión a la base de datos

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'latidoverde');
define('DB_USER', 'root');
define('DB_PASS', ''); // Cambia esto si tienes contraseña

// Configuración de PDO
$pdo_options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
];

// Variable global para la conexión
$pdo = null;

/**
 * Función para obtener la conexión PDO
 * @return PDO
 * @throws Exception
 */
function getConnection() {
    global $pdo;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $GLOBALS['pdo_options']);
            
            // Verificar la conexión
            $pdo->query("SELECT 1");
            
        } catch (PDOException $e) {
            error_log("Error de conexión a la base de datos: " . $e->getMessage());
            throw new Exception("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }
    
    return $pdo;
}

/**
 * Función para cerrar la conexión
 */
function closeConnection() {
    global $pdo;
    $pdo = null;
}

/**
 * Función para verificar si existe un usuario por email
 * @param string $email
 * @return bool
 */
function userExistsByEmail($email) {
    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare("SELECT ID_Usuario FROM usuario WHERE Correo = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() !== false;
    } catch (Exception $e) {
        error_log("Error al verificar usuario por email: " . $e->getMessage());
        return false;
    }
}
    
/**
 * Función para crear un nuevo usuario
 * @param array $userData
 * @return int|false ID del usuario creado o false si falla
 */
function createUser($userData) {
    try {
        $pdo = getConnection();

        $sql = "INSERT INTO usuario (Nombre, Correo, Contrasena, Edad, Fecha_Registro) VALUES (?, ?, ?, ?, NOW())";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            $userData['nombre'],
            $userData['email'],
            $userData['password_hash'],
            $userData['edad']
        ]);
        
        if ($result) {
            return $pdo->lastInsertId();
        }
        
        return false;
        
    } catch (Exception $e) {
        error_log("Error al crear usuario: " . $e->getMessage());
        return false;
    }
}

/**
 * Función para obtener usuario por email
 * @param string $email
 * @return array|false
 */
function getUserByEmail($email) {
    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare("SELECT * FROM usuario WHERE Correo = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    } catch (Exception $e) {
        error_log("Error al obtener usuario: " . $e->getMessage());
        return false;
    }
}

/**
 * Función para verificar la conexión
 * @return bool
 */
function testConnection() {
    try {
        $pdo = getConnection();
        $stmt = $pdo->query("SELECT 1");
        return true;
    } catch (Exception $e) {
        error_log("Test de conexión falló: " . $e->getMessage());
        return false;
    }
}

/**
 * Función para contar registros en una tabla
 * @param string $tabla
 * @return int
 */
function contarRegistros($tabla) {
    try {
        $pdo = getConnection();
        // Sanitizar nombre de tabla (solo permitir letras, números y guión bajo)
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $tabla)) {
            throw new Exception("Nombre de tabla inválido");
        }
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM {$tabla}");
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    } catch (Exception $e) {
        error_log("Error contando registros en {$tabla}: " . $e->getMessage());
        return 0;
    }
}

/**
 * Función para obtener datos de una tabla
 * @param string $tabla
 * @param int $limite
 * @return array
 */
function obtenerDatos($tabla, $limite = 20) {
    try {
        $pdo = getConnection();
        // Sanitizar nombre de tabla
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $tabla)) {
            throw new Exception("Nombre de tabla inválido");
        }
        $stmt = $pdo->prepare("SELECT * FROM {$tabla} ORDER BY id DESC LIMIT :limite");
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Error obteniendo datos de {$tabla}: " . $e->getMessage());
        return [];
    }
}

/**
 * Función para obtener conexión compatible con sistema de reseñas
 * @return PDO
 */
function getConexion() {
    return getConnection();
}

// Verificar conexión al cargar este archivo (opcional)
if (basename($_SERVER['PHP_SELF']) === 'conexion.php') {
    // Solo ejecutar si se accede directamente al archivo
    header('Content-Type: application/json');
    
    if (testConnection()) {
        echo json_encode([
            'success' => true,
            'message' => 'Conexión exitosa a la base de datos',
            'database' => DB_NAME,
            'host' => DB_HOST
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error de conexión a la base de datos'
        ]);
    }
}
?>