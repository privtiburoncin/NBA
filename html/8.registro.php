<?php
require '2.conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = $_POST['cedula'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena']; // Sin cifrar

    try {
        // Verificar si la cédula ya existe
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE cedula = ?");
        $stmt->execute([$cedula]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            echo "<script>alert('La cédula ya está registrada. Por favor, inicie sesión o recupere su cuenta.');</script>";
            header("Location: 5.login.php");
            exit();
        }

        // Insertar nuevo usuario
        $stmt = $conn->prepare("INSERT INTO usuarios (cedula, nombre, apellido, telefono, correo, contrasena) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$cedula, $nombre, $apellido, $telefono, $correo, $contrasena]);
        echo "<script>alert('Registro exitoso.');</script>";
        header("Location: 5.login.php");
        exit();
    } catch (PDOException $e) {
        die("Error al registrar usuario: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="0.styles.css">
</head>
<body>
    <h1>Registro</h1>
    <form method="POST" action="8.registro.php">
        <input type="text" name="cedula" placeholder="Cédula" required>
        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="text" name="apellido" placeholder="Apellido" required>
        <input type="tel" name="telefono" placeholder="Teléfono" required>
        <input type="email" name="correo" placeholder="Correo electrónico" required>
        <input type="password" name="contrasena" placeholder="Contraseña" required>
        <button type="submit">Registrarse</button>
    </form>
</body>
</html>


