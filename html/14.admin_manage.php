<?php
session_start();
require '2.conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = $_POST['cedula'];
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
    $rol = $_POST['rol'];

    try {
        $stmt = $conn->prepare("INSERT INTO administradores (cedula, contrasena, rol) VALUES (?, ?, ?)");
        $stmt->execute([$cedula, $contrasena, $rol]);
        echo "<script>alert('Administrador creado exitosamente.');</script>";
    } catch (PDOException $e) {
        die("Error al crear administrador: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Administradores</title>
    <link rel="stylesheet" href="0.styles.css">
</head>
<body>
    <h1>Gestionar Administradores</h1>
    <form method="POST" action="14.admin_manage.php">
        <input type="text" name="cedula" placeholder="Cédula" required>
        <input type="password" name="contrasena" placeholder="Contraseña" required>
        <select name="rol" required>
            <option value="jefe">Jefe</option>
            <option value="programador">Programador</option>
            <option value="empleado">Empleado</option>
        </select>
        <button type="submit">Crear Administrador</button>
    </form>
</body>
</html>
