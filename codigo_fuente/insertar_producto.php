<?php
session_start();

// Solo permitir si el usuario es admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: index.php");
    exit;
}

include 'Configuracion.php';

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['name'] ?? '');
    $descripcion = trim($_POST['description'] ?? '');
    $precio = floatval($_POST['price'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $imagen = $_FILES['imagen']['name'] ?? '';

    if ($nombre && $precio > 0 && $stock >= 0 && $imagen) {
        $rutaDestino = "imagenes/" . basename($imagen);
        move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino);

        $stmt = $db->prepare("INSERT INTO mis_productos (name, description, price, imagen, stock) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdsi", $nombre, $descripcion, $precio, $imagen, $stock);

        if ($stmt->execute()) {
            $mensaje = "Producto agregado correctamente.";
        } else {
            $mensaje = "Error al guardar: " . $stmt->error;
        }
    } else {
        $mensaje = "Por favor completa todos los campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Producto</title>
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <style>
        .container { padding: 30px; max-width: 600px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Agregar Nuevo Producto</h2>

    <?php if ($mensaje): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($mensaje); ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Nombre:</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Descripción:</label>
            <textarea name="description" class="form-control" rows="3" required></textarea>
        </div>

        <div class="form-group">
            <label>Precio:</label>
            <input type="number" step="0.01" name="price" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Stock:</label>
            <input type="number" name="stock" class="form-control" required min="0">
        </div>

        <div class="form-group">
            <label>Imagen:</label>
            <input type="file" name="imagen" class="form-control" accept="image/*" required>
        </div>

        <button type="submit" class="btn btn-primary">Guardar Producto</button>
        <a href="index.php" class="btn btn-default">Cancelar</a>
    </form>
</div>
</body>
</html>
