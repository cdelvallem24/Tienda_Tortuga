<?php
//session_start();
// include database configuration file
include 'Configuracion.php';

// initializ shopping cart class
include 'La-carta.php';
$cart = new Cart;

// redirect to home if cart is empty
if ($cart->total_items() <= 0) {
    header("Location: index.php");
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$customerID = intval($_SESSION['user_id']);

// set customer ID in session
$_SESSION['sessCustomerID'] = $customerID;

// get customer details by session customer ID
$query = $db->query("SELECT * FROM users WHERE id = " . $customerID);
$custRow = $query->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Pagos - PHP Carrito de compras Tutorial</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <style>
    .container {
        padding: 20px;
    }

    .table {
        width: 65%;
        float: left;
    }

    .shipAddr {
        width: 30%;
        float: left;
        margin-left: 30px;
    }

    .footBtn {
        width: 95%;
        float: left;
    }

    .orderBtn {
        float: right;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading" style="display: flex; align-items: center; justify-content: space-between;">
                <ul class="nav nav-pills" style="margin: 0;">
                    <li role="presentation" class="active"><a href="index.php">Inicio</a></li>
                    <li role="presentation"><a href="VerCarta.php">Carrito de Compras</a></li>
                    <li role="presentation"><a href="Pagos.php">Pagar</a></li>
                    <li role="presentation"><a href="HistorialPedidos.php">Mis Pedidos</a></li>
                    <li role="presentation"><a href="editar_perfil.php">Perfil</a></li>
                    <li role="presentation"><a href="logout.php">Logout</a></li>
                </ul>
                <h3 style="margin: 0;">Tienda Tortuga 🐢</h3>
            </div>


            <div class="panel-body">
                <?php if (isset($_GET['error']) && $_GET['error'] == 'stock'): ?>
                <div class="alert alert-danger">
                    ❌ Lo sentimos, uno o más productos no tienen suficiente stock disponible.
                </div>
                <?php endif; ?>

                <h1>Vista previa de la Orden</h1>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Pricio</th>
                            <th>Cantidad</th>
                            <th>Sub total</th>
                            <th>Imagen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($cart->total_items() > 0) {
                            //get cart items from session
                            $cartItems = $cart->contents();
                            foreach ($cartItems as $item) {
                        ?>
                        <tr>
                            <td><?php echo $item["name"]; ?></td>
                            <td><?php echo 'Q' . $item["price"]; ?></td>
                            <td><?php echo $item["qty"]; ?></td>
                            <td><?php echo 'Q' . $item["subtotal"]; ?></td>
                            <td>
                                <img src="http://127.0.0.1:8080/carrito/imagenes/<?php echo $item["imagen"]; ?>"
                                    style="width: 80px; height: 80px; object-fit: cover;"
                                    alt="<?php echo $item["name"]; ?>">

                            </td>
                        </tr>
                        <?php }
                        } else { ?>
                        <tr>
                            <td colspan="4">
                                <p>No hay articulos en tu carta......</p>
                            </td>
                            <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3"></td>
                            <?php if ($cart->total_items() > 0) { ?>
                            <td class="text-center"><strong>Total <?php echo 'Q' . $cart->total(); ?></strong>
                            </td>
                            <?php } ?>
                        </tr>
                    </tfoot>
                </table>
                <div class="shipAddr">
                    <h4>Detalles de envío</h4>
                    <p><?php echo $custRow['username']; ?></p>
                    <p><?php echo $custRow['email']; ?></p>
                    <p><?php echo $custRow['direccion']; ?></p>
                    <p><?php echo $custRow['celular']; ?></p>
                </div>
                <?php
        // Obtener métodos de pago desde la base de datos
        $metodosPago = $db->query("SELECT * FROM metodos_pago WHERE activo = 1");
        ?>

                <form action="AccionCarta.php" method="get">
                    <input type="hidden" name="action" value="placeOrder">

                    <div class="form-group">
                        <label for="metodo_pago">Selecciona un método de pago:</label>
                        <select name="metodo_pago" id="metodo_pago" class="form-control" required style="width:20%">
                            <option value="">-- Selecciona --</option>
                            <?php while($mp = $metodosPago->fetch_assoc()) { ?>
                            <option value="<?php echo $mp['id']; ?>"><?php echo $mp['nombre']; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="footBtn">
                        <a href="index.php" class="btn btn-warning"><i class="glyphicon glyphicon-menu-left"></i>
                            Continue Comprando</a>
                        <button type="submit" class="btn btn-success orderBtn">Realizar pedido <i
                                class="glyphicon glyphicon-menu-right"></i></button>
                    </div>
                </form>

            </div>
            <div class="panel-footer text-center">
                <a href="terminos.php" target="_blank">Términos y Condiciones</a> |
                <a href="privacidad.php" target="_blank">Política de Privacidad</a>
            </div>
        </div>
        <!--Panek cierra-->
    </div>
</body>

</html>