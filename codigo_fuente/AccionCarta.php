<?php
date_default_timezone_set("America/Lima");
include 'La-carta.php';
$cart = new Cart;
include 'Configuracion.php';
// Después de crear la orden y reducir el stock
include 'enviar_correo.php';


if (isset($_REQUEST['action']) && !empty($_REQUEST['action'])) {

    if ($_REQUEST['action'] == 'addToCart' && !empty($_REQUEST['id'])) {
        $productID = intval($_REQUEST['id']);
        $query = $db->query("SELECT * FROM mis_productos WHERE id = $productID");
        $row = $query->fetch_assoc();

        $itemData = array(
            'id' => $row['id'],
            'name' => $row['name'],
            'price' => $row['price'],
            'qty' => 1,
            'imagen' => $row['imagen']
        );

        $insertItem = $cart->insert($itemData);
        $redirectLoc = $insertItem ? 'VerCarta.php' : 'index.php';
        header("Location: $redirectLoc");
        exit;

    } elseif ($_REQUEST['action'] == 'updateCartItem' && !empty($_REQUEST['id'])) {
        $itemData = array(
            'rowid' => $_REQUEST['id'],
            'qty' => $_REQUEST['qty']
        );
        $updateItem = $cart->update($itemData);
        echo $updateItem ? 'ok' : 'err';
        exit;

    } elseif ($_REQUEST['action'] == 'removeCartItem' && !empty($_REQUEST['id'])) {
        $cart->remove($_REQUEST['id']);
        header("Location: VerCarta.php");
        exit;

    } elseif ($_REQUEST['action'] == 'placeOrder' && $cart->total_items() > 0 && !empty($_SESSION['sessCustomerID'])) {

        $metodoPagoID = isset($_REQUEST['metodo_pago']) ? intval($_REQUEST['metodo_pago']) : 0;
        if ($metodoPagoID <= 0) {
            header("Location: Pagos.php?error=metodo_pago");
            exit;
        }

        // Validar stock
        $cartItems = $cart->contents();
        foreach ($cartItems as $item) {
            $result = $db->query("SELECT stock FROM mis_productos WHERE id = {$item['id']}");
            $product = $result->fetch_assoc();
            if ($product['stock'] < $item['qty']) {
                header("Location: Pagos.php?error=stock&id={$item['id']}");
                exit;
            }
        }

        // Insertar orden
        $insertOrder = $db->query("INSERT INTO orden (customer_id, total_price, metodo_pago_id, created, modified)
            VALUES ('{$_SESSION['sessCustomerID']}', '{$cart->total()}', '$metodoPagoID', NOW(), NOW())");

        if ($insertOrder) {
            $orderID = $db->insert_id;

            // Usamos consultas preparadas en lugar de multi_query (más seguro y sin errores)
            $stmt = $db->prepare("INSERT INTO orden_articulos (order_id, product_id, quantity) VALUES (?, ?, ?)");
            foreach ($cartItems as $item) {
                $stmt->bind_param("iii", $orderID, $item['id'], $item['qty']);
                $stmt->execute();

                // Actualizamos stock
                $db->query("UPDATE mis_productos SET stock = stock - {$item['qty']} WHERE id = {$item['id']}");
            }
            $stmt->close();

            
            // Ejemplo rápido dentro del flujo:
            $customerID = $_SESSION['sessCustomerID'];
            $result = $db->query("SELECT email FROM users WHERE id = $customerID");
            $cliente = $result->fetch_assoc();
            $emailCliente = $cliente['email'];

            $mail->addAddress($emailCliente);
            $mail->Subject = "Tu pedido #$orderID fue confirmado";
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';
            $mail->Body = '
            <!DOCTYPE html>
            <html lang="es">
            <head>
            <meta charset="UTF-8">
            <title>Confirmación de Pedido</title>
            </head>
            <body style="font-family: Arial, sans-serif; background-color: #f7f7f7; margin: 0; padding: 0;">
              <div style="max-width: 600px; margin: 30px auto; background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                <div style="background-color: #007bff; color: #ffffff; text-align: center; padding: 20px;">
                  <h2 style="margin: 0;">¡Gracias por tu compra!</h2>
                </div>
                <div style="padding: 30px;">
                  <p style="font-size: 16px; color: #333;">Hola,</p>
                  <p style="font-size: 16px; color: #333;">Tu pedido <strong>#' . $orderID . '</strong> fue registrado exitosamente.</p>
                  <p style="font-size: 16px; color: #333;">Pronto recibirás una notificación cuando sea enviado.</p>

                  <div style="margin: 30px 0; text-align: center;">
                    <a href="http://localhost:8080/carrito/DetallesPedido.php?id=' . $orderID . '" 
                       style="background-color: #007bff; color: #ffffff; padding: 12px 20px; border-radius: 5px; text-decoration: none; font-weight: bold;">
                       Ver detalles del pedido
                    </a>
                  </div>

                  <hr style="border: none; border-top: 1px solid #eee;">

                  <p style="font-size: 14px; color: #777;">Si tienes alguna pregunta, contáctanos en 
                  <a href="mailto:soporte@tusitio.com" style="color: #007bff; text-decoration: none;">soporte@tusitio.com</a>.</p>
                </div>
                <div style="background-color: #f1f1f1; text-align: center; padding: 15px; color: #999; font-size: 13px;">
                  © ' . date("Y") . ' Tienda tortugita. Todos los derechos reservados.
                </div>
              </div>
            </body>
            </html>';

            $mail->send();

            // Vaciamos el carrito
            $cart->destroy();
            header("Location: OrdenExito.php?id=$orderID");
            exit;

        } else {
            header("Location: Pagos.php?error=insert_order");
            exit;
        }

    } else {
        header("Location: index.php");
        exit;
    }

} else {
    header("Location: index.php");
    exit;
}
?>
