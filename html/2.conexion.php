<?php
// Uso de variables de entorno para configuración
$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'nbasegura';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';

// Configuración de la conexión usando PDO
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    // Mensaje de error menos detallado para evitar exponer información sensible
    die("Error de conexión. Por favor, intenta más tarde.");
}
?>
