<?php
try {
    $conn = new PDO('mysql:host=localhost;dbname=nbasegura;charset=utf8mb4', 'usuario', 'tu_contraseña');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>

