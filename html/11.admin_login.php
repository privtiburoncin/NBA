<?php
session_start();
require '2.conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = trim($_POST['cedula']); // Elimina espacios en blanco al inicio y al final
    $contrasena = $_POST['contrasena'];

    try {
        $stmt = $conn->prepare("SELECT * FROM administradores WHERE cedula = ?");
        $stmt->execute([$cedula]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        // Validar usuario y contraseña
        if ($admin && $contrasena === $admin['contrasena']) { // Comparación directa de texto
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_rol'] = $admin['rol'];
            header("Location: 13.admin_panel.php");
            exit();
        } else {
            echo "<script>alert('Cédula o contraseña incorrecta.');</script>";
        }
    } catch (PDOException $e) {
        die("Error al iniciar sesión: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="0.styles.css">
</head>
<body>
    <h1>Inicio de Sesión Administrador</h1>
    <form method="POST" action="11.admin_login.php">
        <label for="cedula">Cédula:</label>
        <input type="text" name="cedula" id="cedula" placeholder="Cédula" required>
        
        <label for="contrasena">Contraseña:</label>
        <input type="password" name="contrasena" id="contrasena" placeholder="Contraseña" required>
        
        <button type="submit">Iniciar Sesión</button>
    </form>
</body>
</html>

