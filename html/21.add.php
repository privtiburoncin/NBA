<?php
session_start();
require '2.conexion.php';

if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_rol'])) {
    header("Location: 11.admin_login.php");
    exit();
}

$admin_rol = $_SESSION['admin_rol'];

// Validar que solo el jefe y programador puedan acceder
if ($admin_rol !== 'jefe' && $admin_rol !== 'programador') {
    die("Acceso no autorizado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = trim($_POST['cedula']);
    $contrasena = $_POST['contrasena'];
    $rol = $_POST['rol'];

    // Validar que el programador no pueda crear un jefe
    if ($admin_rol === 'programador' && $rol === 'jefe') {
        die("No tienes permiso para agregar un administrador con el rol de jefe.");
    }

    try {
        // Verificar si la cédula ya existe
        $stmt = $conn->prepare("SELECT * FROM administradores WHERE cedula = ?");
        $stmt->execute([$cedula]);
        $existente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existente) {
            die("Error: Ya existe un administrador con esta cédula.");
        }

        // Insertar nuevo administrador
        $stmt = $conn->prepare("INSERT INTO administradores (cedula, contrasena, rol) VALUES (?, ?, ?)");
        $stmt->execute([$cedula, $contrasena, $rol]);

        echo "<script>alert('Administrador agregado exitosamente.');</script>";
        header("Location: 13.admin_panel.php");
        exit();
    } catch (PDOException $e) {
        die("Error al agregar administrador: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Administrador</title>
    <link rel="stylesheet" href="0.styles.css">
</head>
<body>
    <h1>Agregar Administrador</h1>
    <form method="POST" action="21.add.php">
        <label for="cedula">Cédula:</label>
        <input type="text" name="cedula" id="cedula" required>

        <label for="contrasena">Contraseña:</label>
        <input type="password" name="contrasena" id="contrasena" required>

        <label for="rol">Rol:</label>
        <select name="rol" id="rol" required>
            <option value="empleado">Empleado</option>
            <?php if ($admin_rol === 'jefe'): ?>
                <option value="programador">Programador</option>
                <option value="jefe">Jefe</option>
            <?php endif; ?>
        </select>

        <button type="submit">Agregar</button>
    </form>
    <a href="13.admin_panel.php">Regresar al panel</a>
</body>
</html>
