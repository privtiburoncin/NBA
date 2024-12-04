<?php
session_start();
require '2.conexion.php';

if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_rol'])) {
    header("Location: 11.admin_login.php");
    exit();
}

$admin_rol = $_SESSION['admin_rol'];

// Verificar el rol del usuario para limitar acciones
if ($admin_rol === 'empleado') {
    die("Acceso no autorizado.");
}

// Mostrar lista de administradores
try {
    $stmt = $conn->prepare("SELECT * FROM administradores");
    $stmt->execute();
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener administradores: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>
    <link rel="stylesheet" href="0.styles.css">
</head>
<body>
    <h1>Panel de Administrador</h1>
    <table>
        <thead>
            <tr>
                <th>Cédula</th>
                <th>Rol</th>
                <?php if ($admin_rol === 'jefe' || $admin_rol === 'programador'): ?>
                    <th>Acciones</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($admins as $admin): ?>
                <tr>
                    <td><?php echo htmlspecialchars($admin['cedula']); ?></td>
                    <td><?php echo htmlspecialchars($admin['rol']); ?></td>
                    <?php if ($admin_rol === 'jefe' || $admin_rol === 'programador'): ?>
                        <td>
                            <?php if ($admin_rol === 'jefe' || ($admin_rol === 'programador' && $admin['rol'] !== 'jefe')): ?>
                                <a href="15.admin_delete.php?id=<?php echo $admin['id']; ?>">Eliminar</a>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ($admin_rol === 'jefe' || $admin_rol === 'programador'): ?>
        <h2>Agregar Nuevo Administrador</h2>
        <form method="POST" action="14.admin_add.php">
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
    <?php endif; ?>
</body>
</html>
