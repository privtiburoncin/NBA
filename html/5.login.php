<?php

session_start();
require '2.conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    try {
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE correo = ? AND contrasena = ?");
        $stmt->execute([$correo, $contrasena]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre']; // Asegúrate de que se asigne el nombre aquí
            header("Location: index.php");
            exit();
        } else {
            echo "<script>alert('Correo o contraseña incorrectos.');</script>";
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
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="0.styles.css">
</head>
<body>
    <h1>Iniciar Sesión</h1>
    <form method="POST" action="5.login.php">
        <label for="correo">Correo Electrónico:</label>
        <input type="email" name="correo" placeholder="Correo electrónico" required>

        <label for="contrasena">Contraseña:</label>
        <input type="password" name="contrasena" placeholder="Contraseña" required>

        <button type="submit">Iniciar Sesión</button>
    </form>
    <a href="10.recuperacion.php">¿Olvidaste tu contraseña?</a>

</body>
</html>

