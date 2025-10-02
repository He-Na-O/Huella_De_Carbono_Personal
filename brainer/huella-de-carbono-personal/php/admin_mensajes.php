<?php
// admin_mensajes.php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "latidoverde";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("set names utf8");
    
    // Obtener mensajes
    $sql = "SELECT * FROM contacto ORDER BY Fecha_Contacto DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Mensajes de Contacto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">
            <i class="bi bi-envelope-open text-success me-2"></i>
            Mensajes de Contacto
        </h1>
        
        <?php if (empty($mensajes)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                No hay mensajes de contacto.
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($mensajes as $mensaje): ?>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="bi bi-person-circle me-1"></i>
                                    <?php echo htmlspecialchars($mensaje['Nombre']); ?>
                                </h6>
                                <small class="text-muted">
                                    <?php echo date('d/m/Y H:i', strtotime($mensaje['Fecha_Contacto'])); ?>
                                </small>
                            </div>
                            <div class="card-body">
                                <p class="card-text">
                                    <strong>Email:</strong> 
                                    <a href="mailto:<?php echo htmlspecialchars($mensaje['Email']); ?>">
                                        <?php echo htmlspecialchars($mensaje['Email']); ?>
                                    </a>
                                </p>
                                <hr>
                                <p class="card-text">
                                    <?php echo nl2br(htmlspecialchars($mensaje['Mensaje'])); ?>
                                </p>
                            </div>
                            <div class="card-footer">
                                <small class="text-muted">ID: <?php echo $mensaje['ID_Contacto']; ?></small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>