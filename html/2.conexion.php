<?php
try {
    $conn = new PDO('mysql:host=192.168.128.237;dbname=nbasegura;charset=utf8mb4', 'usuario', 'Hola#comovas.12');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
