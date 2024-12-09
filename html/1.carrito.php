<?php
session_start();
require '2.conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: 5.login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Obtener los productos del carrito desde la base de datos
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

// Manejar el envío y pago después de la compra
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalizar_compra'])) {
    try {
        // Insertar pedido
        $stmt = $conn->prepare("INSERT INTO pedidos (usuario_id, total) VALUES (?, ?)");
        $stmt->execute([$usuario_id, $total_con_envio]);
        $pedido_id = $conn->lastInsertId();

        if (!$pedido_id) {
            die("Error: No se pudo insertar el pedido en la base de datos.");
        }

        // Insertar detalles del pedido
        foreach ($carrito as $item) {
            $stmt = $conn->prepare("INSERT INTO detalles_pedido (pedido_id, producto_id, cantidad, subtotal) VALUES (?, ?, ?, ?)");
            $subtotal = $item['precio'] * $item['cantidad'];
            $stmt->execute([$pedido_id, $item['id'], $item['cantidad'], $subtotal]);
        }

        // Vaciar el carrito
        $stmt = $conn->prepare("DELETE FROM carrito WHERE usuario_id = ?");
        $stmt->execute([$usuario_id]);

        // Guardar en sesión para redirigir después del formulario de envío y pago
        $_SESSION['ultimo_pedido_id'] = $pedido_id;
        $_SESSION['total_con_envio'] = $total_con_envio;

        // Mostrar formulario para detalles de envío y pago
        $mostrar_formulario = true;

    } catch (PDOException $e) {
        die("Error al finalizar compra: " . $e->getMessage());
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_envio_pago'])) {
    // Guardar datos de envío y pago en la base de datos
    $direccion = $_POST['direccion'];
    $ciudad = $_POST['ciudad'];
    $codigo_postal = $_POST['codigo_postal'];
    $telefono = $_POST['telefono'];
    $numero_tarjeta = $_POST['numero_tarjeta'];
    $nombre_titular = $_POST['nombre_titular'];
    $fecha_expiracion = $_POST['fecha_expiracion'];
    $codigo_seguridad = $_POST['codigo_seguridad'];

    try {
        // Insertar datos de envío
        $stmt = $conn->prepare("INSERT INTO envios (pedido_id, direccion, ciudad, codigo_postal, telefono) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['ultimo_pedido_id'], $direccion, $ciudad, $codigo_postal, $telefono]);

        // Insertar datos de pago
        $stmt = $conn->prepare("INSERT INTO tarjetas_credito (pedido_id, numero_tarjeta, nombre_titular, fecha_expiracion, codigo_seguridad) 
                                VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['ultimo_pedido_id'], $numero_tarjeta, $nombre_titular, $fecha_expiracion, $codigo_seguridad]);

        // Redirigir a una página de confirmación
        echo "<script>alert('Compra finalizada con éxito.');</script>";
        header("Location: 9.confirmacion.php");
        exit();

    } catch (PDOException $e) {
        die("Error al guardar los detalles de envío y pago: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link rel="stylesheet" href="\html\0.styles.css">
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
                        <th>Hombre/Mujer</th>
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
                            <td><img src="Imagenes_Imagen/<?php echo htmlspecialchars($item['imagen_url']); ?>" alt="<?php echo htmlspecialchars($item['nombre']); ?>" width="100"></td>
                            <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                            <td>
                                <!-- Botón para elegir Hombre/Mujer -->
                                <select name="genero" class="carrito-genero">
                                    <option value="Hombre">Hombre</option>
                                    <option value="Mujer">Mujer</option>
                                </select>
                            </td>
                            <td>
                                <!-- Selector de talla -->
                                <select name="talla" class="carrito-talla">
                                    <?php
                                    // Determinar tipo de producto y mostrar tallas adecuadas
                                    if (stripos($item['nombre'], 'zapatilla') !== false) {
                                        // Tallas para zapatillas
                                        $tallas = range(6.5, 11, 0.5);
                                    } elseif (preg_match('/gorra|media|jersey|camiseta|sueter|short|shorts/i', $item['nombre'])) {
                                        // Tallas para otras prendas
                                        $tallas = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
                                    } else {
                                        $tallas = []; // Si no hay categoría específica
                                    }
                                    foreach ($tallas as $talla) {
                                        echo "<option value='$talla'>$talla</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                            <td><?php echo htmlspecialchars($item['cantidad']); ?></td>
                            <td>$<?php echo htmlspecialchars($item['precio']); ?></td>
                            <td>$<?php echo htmlspecialchars($item['precio'] * $item['cantidad']); ?></td>
                            <td>
                                <!-- Botón para eliminar producto -->
                                <form method="POST" action="1.carrito.php">
                                    <input type="hidden" name="producto_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" name="eliminar_producto" class="btn-eliminar">Eliminar</button>
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
            <form method="POST" action="detalles_envio.php">
                <input type="hidden" name="total_con_envio" value="<?php echo $total_con_envio; ?>">
                <button type="submit" class="btn-finalizar">Pasar a la Siguiente Página</button>
            </form>
        <?php else: ?>
            <p>Tu carrito está vacío.</p>
        <?php endif; ?>
    </div>
</body>
</html>

