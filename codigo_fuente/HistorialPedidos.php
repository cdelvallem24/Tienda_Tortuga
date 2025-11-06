<?php
session_start(); // Asegúrate de que esté aquí

include 'Configuracion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$customerID = intval($_SESSION['user_id']); // Asegúrate de que es un número

// Obtener todas las órdenes del usuario
$query = $db->query("SELECT o.*, m.nombre AS metodo_pago 
                     FROM orden o 
                     LEFT JOIN metodos_pago m ON o.metodo_pago_id = m.id 
                     WHERE o.customer_id = $customerID 
                     ORDER BY o.created DESC");

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Pedidos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2>Historial de Pedidos</h2>
    <a href="index.php" class="btn btn-primary">Volver al inicio</a>
    <br><br>
    <?php if($query->num_rows > 0){ ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Pedido</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Método de Pago</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php while($row = $query->fetch_assoc()){ ?>
                <tr>
                    <td>#<?php echo $row['id']; ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($row['created'])); ?></td>
                    <td><?php echo 'Q' . number_format($row['total_price'], 0, '.', ','); ?></td>
                    <td><?php echo $row['metodo_pago'] ?? 'No especificado'; ?></td>
                    <td>
                        <a href="DetallesPedido.php?id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm">Ver Detalles</a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p>No tienes pedidos anteriores.</p>
    <?php } ?>

        <div class="panel-footer text-center">
        <a href="terminos.php" target="_blank">Términos y Condiciones</a> | 
        <a href="privacidad.php" target="_blank">Política de Privacidad</a>
    </div>

</div>
</body>
</html>
