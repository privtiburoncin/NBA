<?php
session_start();
require '2.conexion.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_rol'] !== 'jefe') {
    header("Location: 11.admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_id'])) {
    $admin_id = $_POST['admin_id'];

    try {
        // Eliminar administrador
        $stmt = $conn->prepare("DELETE FROM administradores WHERE id = ?");
        $stmt->execute([$admin_id]);
        echo "<script>alert('Administrador eliminado correctamente.');</script>";
        header("Location: 14.admin_manage.php");
        exit();
    } catch (PDOException $e) {
        die("Error al eliminar administrador: " . $e->getMessage());
    }
}
?>
