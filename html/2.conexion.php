<?php
try {
    $conn = new PDO('mysql:host=172.29.126.159;dbname=nbasegura;charset=utf8mb4', 'usuario', 'Hola#comovas.12');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
