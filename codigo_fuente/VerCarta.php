<?php
// initializ shopping cart class
include 'La-carta.php';
$cart = new Cart;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>View Cart - PHP Shopping Cart Tutorial</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <style>
    .container {
        padding: 20px;
    }

    input[type="number"] {
        width: 20%;
    }
    </style>
    <script>
    function updateCartItem(obj, id) {
        $.get("AccionCarta.php", {
            action: "updateCartItem",
            id: id,
            qty: obj.value
        }, function(data) {
            if (data == 'ok') {
                location.reload();
            } else {
                alert('Cart update failed, please try again.');
            }
        });
    }
    </script>
</head>
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


                <h1>Carrito de compras</h1>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Sub total</th>
                            <th>Imagen</th>
                            <th>&nbsp;</th>
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
                            <td><?php echo 'Q' . $item["price"]?></td>
                            <td><input type="number" 
       class="form-control text-center" 
       value="<?= $item['qty']; ?>" 
       onchange="updateCartItem(this, '<?= $item['rowid']; ?>')"></td>
                            <td><?php echo 'Q' . $item["subtotal"]; ?></td>
                            <td>
                                <img src="http://127.0.0.1:8080/carrito/imagenes/<?php echo $item["imagen"]; ?>"
                                    style="width: 80px; height: 80px; object-fit: cover;"
                                    alt="<?php echo $item["name"]; ?>">

                            </td>
                            <td>
                                <a href="AccionCarta.php?action=removeCartItem&id=<?php echo $item["rowid"]; ?>"
                                    class="btn btn-danger" onclick="return confirm('Confirma eliminar?')"><i
                                        class="glyphicon glyphicon-trash"></i></a>
                            </td>
                        </tr>
                        <?php }
                        } else { ?>
                        <tr>
                            <td colspan="5">
                                <p>No has solicitado ningún producto.....</p>
                            </td>
                            <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td><a href="index.php" class="btn btn-warning"><i
                                        class="glyphicon glyphicon-menu-left"></i> Volver a la tienda</a></td>
                            <td colspan="2"></td>
                            <?php if ($cart->total_items() > 0) { ?>
                            <td class="text-center"><strong>Total <?php echo 'Q' . $cart->total() ?></strong>
                            </td>
                            <td><a href="Pagos.php" class="btn btn-success btn-block">Pagos <i
                                        class="glyphicon glyphicon-menu-right"></i></a></td>
                            <?php } ?>
                        </tr>
                    </tfoot>
                </table>

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