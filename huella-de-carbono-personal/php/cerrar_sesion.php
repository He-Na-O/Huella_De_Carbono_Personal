<?php
// Archivo: cerrar_sesion.php
// Guardar en: /brainer/huella-de-carbono-personal/php/cerrar_sesion.php

session_start();
header('Content-Type: application/json');

// Destruir todas las variables de sesión
$_SESSION = array();

// Destruir la sesión
session_destroy();

echo json_encode([
    'status' => 'success',
    'message' => 'Sesión cerrada correctamente'
]);
?>