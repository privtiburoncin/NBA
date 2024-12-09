<?php
session_start();
require '2.conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: 5.login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Eliminar producto del carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_producto'])) {
    try {
        $producto_id = $_POST['producto_id'];
        $stmt = $conn->prepare("DELETE FROM carrito WHERE usuario_id = ? AND producto_id = ?");
        $stmt->execute([$usuario_id, $producto_id]);
        header("Location: 1.carrito.php");
        exit();
    } catch (PDOException $e) {
        die("Error al eliminar producto: " . $e->getMessage());
    }
}

// Obtener productos del carrito
try {
    $stmt = $conn->prepare("SELECT c.cantidad, p.* 
                            FROM carrito c 
                            JOIN productos p ON c.producto_id = p.id 
                            WHERE c.usuario_id = ?");
    $stmt->execute([$usuario_id]);
    $carrito = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener productos del carrito: " . $e->getMessage());
}

// Calcular el total
$precio_envio = 20;
$total = 0;
foreach ($carrito as $item) {
    $total += $item['precio'] * $item['cantidad'];
}
$total_con_envio = $total + $precio_envio;

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link rel="stylesheet" href="0.styles.css">
</head>
<body>
    <h1>Carrito de Compras</h1>
    <div class="carrito-container">
        <?php if (!empty($carrito)): ?>
            <table class="carrito-tabla">
                <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>Producto</th>
                        <th>Talla</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Subtotal</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($carrito as $item): ?>
                        <tr>
                            <td><img src="Imagenes_Imagen/<?php echo htmlspecialchars($item['imagen_url']); ?>" width="100"></td>
                            <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                            <td>
                                <select name="talla">
                                    <option value="S">S</option>
                                    <option value="M">M</option>
                                    <option value="L">L</option>
                                    <option value="XL">XL</option>
                                </select>
                            </td>
                            <td><?php echo htmlspecialchars($item['cantidad']); ?></td>
                            <td>$<?php echo htmlspecialchars($item['precio']); ?></td>
                            <td>$<?php echo htmlspecialchars($item['precio'] * $item['cantidad']); ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="producto_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" name="eliminar_producto">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="carrito-total">
                <p>Precio de Envío: $<?php echo $precio_envio; ?></p>
                <p><strong>Total con Envío: $<?php echo $total_con_envio; ?></strong></p>
            </div>
        <?php else: ?>
            <p>Tu carrito está vacío.</p>
        <?php endif; ?>
    </div>
</body>
</html>
