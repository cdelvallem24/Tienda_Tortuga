<?php
session_start();
include 'Configuracion.php';

// Verificar si es admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$orderID = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Obtener información de la orden
$query = $db->query("SELECT o.*, u.username, u.email, m.nombre AS metodo_pago 
                     FROM orden o
                     INNER JOIN users u ON o.customer_id = u.id
                     INNER JOIN metodos_pago m ON o.metodo_pago_id = m.id
                     WHERE o.id = $orderID");
$order = $query->fetch_assoc();

// Obtener productos de la orden
$items = $db->query("SELECT p.name, p.price, oa.quantity 
                     FROM orden_articulos oa
                     INNER JOIN mis_productos p ON oa.product_id = p.id
                     WHERE oa.order_id = $orderID");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Detalle de Venta</title>
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2>Detalle del Pedido #<?php echo $order['id']; ?></h2>
    <p><strong>Cliente:</strong> <?php echo htmlspecialchars($order['username']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
    <p><strong>Método de pago:</strong> <?php echo htmlspecialchars($order['metodo_pago']); ?></p>
    <p><strong>Fecha:</strong> <?php echo $order['created']; ?></p>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total = 0;
            while ($item = $items->fetch_assoc()):
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
            ?>
            <tr>
                <td><?php echo $item['name']; ?></td>
                <td>Q<?php echo number_format($item['price'], 2); ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td>Q<?php echo number_format($subtotal, 2); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">Total:</th>
                <th>Q<?php echo number_format($total, 2); ?></th>
            </tr>
        </tfoot>
    </table>

    <a href="ventas.php" class="btn btn-primary">Volver</a>
</div>
</body>
</html>
