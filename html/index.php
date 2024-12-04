<?php
session_start();
require '2.conexion.php';

// Obtener todas las categorías
try {
    $stmt = $conn->prepare("SELECT DISTINCT categoria FROM productos");
    $stmt->execute();
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener categorías: " . $e->getMessage());
}

// Manejar la búsqueda y el filtro
$categoria_seleccionada = isset($_GET['categoria']) ? $_GET['categoria'] : '';
$termino_busqueda = isset($_GET['buscar']) ? $_GET['buscar'] : '';

$query = "SELECT * FROM productos WHERE 1";
$params = [];

if (!empty($categoria_seleccionada)) {
    $query .= " AND categoria = ?";
    $params[] = $categoria_seleccionada;
}

if (!empty($termino_busqueda)) {
    $query .= " AND (nombre LIKE ? OR descripcion LIKE ?)";
    $params[] = "%$termino_busqueda%";
    $params[] = "%$termino_busqueda%";
}

// Obtener productos basados en filtros
try {
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener productos: " . $e->getMessage());
}

// Manejar el envío al carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['producto_id'], $_POST['cantidad'])) {
    $producto_id = (int)$_POST['producto_id'];
    $cantidad = (int)$_POST['cantidad'];

    try {
        // Guardar producto en el carrito de la base de datos
        $stmt = $conn->prepare("INSERT INTO carrito (usuario_id, producto_id, cantidad) VALUES (?, ?, ?)
                                ON DUPLICATE KEY UPDATE cantidad = cantidad + ?");
        $stmt->execute([$_SESSION['usuario_id'], $producto_id, $cantidad, $cantidad]);

        echo "<script>alert('Producto añadido al carrito');</script>";
        echo "<script>window.location.href = 'index.php';</script>";
        exit();
    } catch (PDOException $e) {
        die("Error al añadir producto al carrito: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos NBA</title>
    <link rel="stylesheet" href="0.styles.css">
</head>
<body>
<header>
    <div class="logo">
        <img src="Imagenes_Imagen/logonba.png" alt="NBA Logo">
    </div>
    <nav>
        <ul>
            <li><a href="index.php">Inicio</a></li>
            <li><a href="#productos">Productos</a></li>
            <li><a href="1.carrito.php">Carrito</a></li>
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <li><a href="6.logout.php">Cerrar Sesión (<?php echo isset($_SESSION['usuario_nombre']) ? htmlspecialchars($_SESSION['usuario_nombre']) : 'Usuario'; ?>)</a></li>
            <?php else: ?>
                <li><a href="5.login.php">Iniciar Sesión</a></li>
                <li><a href="8.registro.php">Registrarse</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
    <main>
        <section id="productos">
            <h1>Productos NBA</h1>
            <form method="GET" action="index.php" class="filtros">
                <select name="categoria">
                    <option value="">Todas las Categorías</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?php echo htmlspecialchars($categoria['categoria']); ?>" <?php echo ($categoria_seleccionada === $categoria['categoria']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($categoria['categoria']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="buscar" placeholder="Buscar productos..." value="<?php echo htmlspecialchars($termino_busqueda); ?>">
                <button type="submit">Buscar</button>
            </form>
            <div class="productos">
                <?php if (!empty($productos)): ?>
                    <?php foreach ($productos as $producto): ?>
                        <div class="producto">
                            <img src="Imagenes_Imagen/<?php echo htmlspecialchars($producto['imagen_url']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                            <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                            <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                            <p>Precio: $<?php echo htmlspecialchars($producto['precio']); ?></p>
                            <?php if (isset($_SESSION['usuario_id'])): ?>
                                <form method="POST" action="index.php">
                                    <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                                    <label for="cantidad_<?php echo $producto['id']; ?>">Cantidad:</label>
                                    <input type="number" id="cantidad_<?php echo $producto['id']; ?>" name="cantidad" value="1" min="1">
                                    <button type="submit">Añadir al Carrito</button>
                                </form>
                            <?php else: ?>
                                <p><a href="5.login.php">Inicie sesión para añadir al carrito.</a></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No se encontraron productos.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 Tienda Oficial NBA - Todos los derechos reservados</p>
    </footer>
</body>
</html>