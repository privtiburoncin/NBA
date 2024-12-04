<?php
session_start();
require '2.conexion.php';

if (!isset($_SESSION['ultimo_pedido_id']) || !isset($_SESSION['total_con_envio'])) {
    die("Error: No se pudo encontrar la información del pedido.");
}

$pedido_id = $_SESSION['ultimo_pedido_id'];
$total_con_envio = $_SESSION['total_con_envio'];

// Obtener los detalles del pedido
try {
    $stmt = $conn->prepare("SELECT dp.*, p.nombre, p.imagen_url 
                            FROM detalles_pedido dp 
                            JOIN productos p ON dp.producto_id = p.id 
                            WHERE dp.pedido_id = ?");
    $stmt->execute([$pedido_id]);
    $detalles_pedido = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$detalles_pedido) {
        die("Error: No se encontraron detalles del pedido.");
    }
} catch (PDOException $e) {
    die("Error al obtener detalles del pedido: " . $e->getMessage());
}
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Pedido</title>
    <link rel="stylesheet" href="0.styles.css">
</head>
<body>
    <h1>Detalles del Pedido</h1>
    <div class="pedido-container">
        <?php if (!empty($detalles_pedido)): ?>
            <table class="pedido-tabla">
                <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Talla</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($detalles_pedido as $detalle): ?>
                        <tr>
                            <td><img src="Imagenes_Imagen/<?php echo htmlspecialchars($detalle['imagen_url']); ?>" alt="<?php echo htmlspecialchars($detalle['nombre']); ?>" width="100"></td>
                            <td><?php echo htmlspecialchars($detalle['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($detalle['cantidad']); ?></td>
                            <td><?php echo htmlspecialchars($detalle['talla']); ?></td>
                            <td>$<?php echo htmlspecialchars($detalle['subtotal']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="pedido-total">
                <p><strong>Total Pagado (incluido envío): $<?php echo $total_con_envio; ?></strong></p>
            </div>
        <?php else: ?>
            <p>No se encontraron detalles para este pedido.</p>
        <?php endif; ?>
    </div>
</body>
</html>
