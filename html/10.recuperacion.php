<?php
require '2.conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = $_POST['cedula'];
    $nueva_contrasena = $_POST['nueva_contrasena']; // Sin cifrar

    try {
        // Verificar si la cédula existe
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE cedula = ?");
        $stmt->execute([$cedula]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            // Actualizar la contraseña
            $stmt = $conn->prepare("UPDATE usuarios SET contrasena = ? WHERE cedula = ?");
            $stmt->execute([$nueva_contrasena, $cedula]);
            echo "<script>alert('Contraseña actualizada con éxito.');</script>";
            header("Location: 5.login.php");
            exit();
        } else {
            echo "<script>alert('Cédula no encontrada. Por favor, verifique los datos.');</script>";
        }
    } catch (PDOException $e) {
        die("Error al actualizar la contraseña: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <link rel="stylesheet" href="0.styles.css">
</head>
<body>
    <h1>Recuperar Contraseña</h1>
    <form method="POST" action="10.recuperacion.php">
        <input type="text" name="cedula" placeholder="Cédula" required>
        <input type="password" name="nueva_contrasena" placeholder="Nueva Contraseña" required>
        <button type="submit">Actualizar Contraseña</button>
    </form>
</body>
</html>
