<?php
session_start();
require '2.conexion.php';

if (!isset($_SESSION['ultimo_pedido_id'])) {
    die("Error: No se pudo encontrar el ID del pedido.");
}

$pedido_id = $_SESSION['ultimo_pedido_id'];

// Obtener detalles del pedido
try {
    $stmt = $conn->prepare("SELECT p.total, e.direccion, e.ciudad, e.codigo_postal, e.telefono, t.numero_tarjeta, t.nombre_titular 
                            FROM pedidos p
                            JOIN envios e ON p.id = e.pedido_id
                            JOIN tarjetas_credito t ON p.id = t.pedido_id
                            WHERE p.id = ?");
    $stmt->execute([$pedido_id]);
    $detalle_pedido = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$detalle_pedido) {
        die("Error: No se encontraron detalles del pedido.");
    }
} catch (PDOException $e) {
    die("Error al obtener los detalles del pedido: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Pedido</title>
    <link rel="stylesheet" href="0.styles.css">
</head>
<body>
    <h1>Confirmación de Pedido</h1>
    <div class="confirmacion-container">
        <h2>Detalles del Pedido</h2>
        <p><strong>Total de la Compra:</strong> $<?php echo htmlspecialchars($detalle_pedido['total']); ?></p>
        <h2>Detalles de Envío</h2>
        <p><strong>Dirección:</strong> <?php echo htmlspecialchars($detalle_pedido['direccion']); ?></p>
        <p><strong>Ciudad:</strong> <?php echo htmlspecialchars($detalle_pedido['ciudad']); ?></p>
        <p><strong>Código Postal:</strong> <?php echo htmlspecialchars($detalle_pedido['codigo_postal']); ?></p>
        <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($detalle_pedido['telefono']); ?></p>
        <h2>Detalles de Pago</h2>
        <p><strong>Número de Tarjeta:</strong> **** **** **** <?php echo substr(htmlspecialchars($detalle_pedido['numero_tarjeta']), -4); ?></p>
        <p><strong>Nombre del Titular:</strong> <?php echo htmlspecialchars($detalle_pedido['nombre_titular']); ?></p>
        <p>¡Gracias por tu compra! Tu pedido ha sido procesado con éxito.</p>
    </div>
</body>
</html>
