<?php
// Configuración de la conexión a la base de datos usando PDO
$host = 'localhost';
$dbname = 'nbasegura';
$username = 'root';
$password = '';

// Crear la conexión usando PDO
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    // Mensaje de error menos detallado para evitar exponer información sensible
    die("Error de conexión. Por favor, intenta más tarde.");
}
?>
