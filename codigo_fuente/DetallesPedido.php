<?php
session_start();
include 'Configuracion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$orderID = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Obtener orden
$query = $db->query("SELECT o.*, m.nombre AS metodo_pago 
                     FROM orden o 
                     LEFT JOIN metodos_pago m ON o.metodo_pago_id = m.id 
                     WHERE o.id = $orderID AND o.customer_id = ".$_SESSION['user_id']);

if ($query->num_rows == 0) {
    echo "Orden no encontrada o no tienes permiso para verla.";
    exit;
}

$order = $query->fetch_assoc();

// Obtener productos de la orden
$itemsQuery = $db->query("SELECT oa.*, p.name, p.price 
                          FROM orden_articulos oa 
                          JOIN mis_productos p ON oa.product_id = p.id 
                          WHERE oa.order_id = $orderID");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles del Pedido #<?php echo $orderID; ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2>Detalles del Pedido #<?php echo $orderID; ?></h2>
    <p><strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created'])); ?></p>
    <p><strong>Método de pago:</strong> <?php echo $order['metodo_pago'] ?? 'No especificado'; ?></p>
    <p><strong>Total:</strong> <?php echo 'Q' . number_format($order['total_price'], 0, '.', ','); ?></p>
    <hr>

    <h4>Productos</h4>
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
            <?php while($item = $itemsQuery->fetch_assoc()){ ?>
            <tr>
                <td><?php echo $item['name']; ?></td>
                <td><?php echo 'Q' . number_format($item['price'], 0, '.', ','); ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td><?php echo 'Q' . number_format($item['price'] * $item['quantity'], 0, '.', ','); ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <a href="HistorialPedidos.php" class="btn btn-default">Volver al historial</a>
</div>
</body>
</html>
