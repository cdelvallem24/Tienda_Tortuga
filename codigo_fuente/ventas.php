<?php
session_start();
include 'Configuracion.php';

// Verificar si el usuario es admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Consulta de todas las órdenes
$sql = "SELECT o.id, u.username, u.email, o.total_price, o.created, m.nombre AS metodo_pago
        FROM orden o
        INNER JOIN users u ON o.customer_id = u.id
        INNER JOIN metodos_pago m ON o.metodo_pago_id = m.id
        ORDER BY o.created DESC";
$result = $db->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Ventas - Panel Admin</title>
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <style>
        .container { padding: 20px; }
        table th, table td { text-align: center; }
    </style>
</head>
<body>
<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading" style="display: flex; align-items: center; justify-content: space-between;">
            <ul class="nav nav-pills" style="margin: 0;">
                <li><a href="index.php">Inicio</a></li>
                <li><a href="insertar_producto.php">Agregar Producto</a></li>
                <li class="active"><a href="ventas.php">Ventas</a></li>
                <li><a href="logout.php">Salir</a></li>
            </ul>
            <h3 style="margin: 0;">Panel de Ventas 🧾</h3>
        </div>

        <div class="panel-body">
            <h2>Historial de Ventas</h2>
            <?php if ($result && $result->num_rows > 0): ?>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID Pedido</th>
                            <th>Cliente</th>
                            <th>Email</th>
                            <th>Total</th>
                            <th>Método de Pago</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td>Q<?php echo number_format($row['total_price'], 2); ?></td>
                                <td><?php echo htmlspecialchars($row['metodo_pago']); ?></td>
                                <td><?php echo $row['created']; ?></td>
                                <td>
                                    <a href="detalle_venta.php?id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm">
                                        Ver Detalle
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No se encontraron ventas registradas.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
