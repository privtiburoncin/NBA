<?php
// Datos de conexión a la base de datos
$servername = "localhost"; // Servidor (localhost si estás trabajando localmente)
$username = "usuario"; // Cambia 'usuario' por el nombre del usuario que creaste en MySQL
$password = "contraseña"; // Cambia 'contraseña' por la contraseña que asignaste a ese usuario
$dbname = "nbasegura"; // Nombre de la base de datos que creaste (nbasegura)

// Configuración de la conexión usando PDO
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    // Mensaje de error menos detallado para evitar exponer información sensible
    die("Error de conexión. Por favor, intenta más tarde.");
}
?>
