<?php
require __DIR__ . "/config.php";
$cn = db();

titulo("Prueba de integración: login, carrito, pedido, historial");

try {
    $cn->begin_transaction();

    // Usuario
    $correo = "flujo@correo.com";
    $passPlano = "Flujo123";
    $hash = password_hash($passPlano, PASSWORD_BCRYPT);
    $nombre = "Flujo Demo";
    $rol = "cliente";

    $cn->query("DELETE FROM usuarios WHERE correo = '" . $cn->real_escape_string($correo) . "'");

    $iu = $cn->prepare("INSERT INTO usuarios(nombre, correo, contrasena, rol, fecha_registro) VALUES(?, ?, ?, ?, NOW())");
    $iu->bind_param("ssss", $nombre, $correo, $hash, $rol);
    $iu->execute();
    $idUsuario = $iu->insert_id;
    $iu->close();

    // Login simulado
    $sl = $cn->prepare("SELECT id, contrasena FROM usuarios WHERE correo = ?");
    $sl->bind_param("s", $correo);
    $sl->execute();
    $row = $sl->get_result()->fetch_assoc();
    $sl->close();
    $ok = password_verify($passPlano, $row['contrasena']);
    assert_igual(true, $ok, "Login correcto para flujo");

    // Producto
    $ip = $cn->prepare("INSERT INTO productos(nombre, descripcion, precio, stock, imagen, fecha_registro) VALUES(?, ?, ?, ?, ?, NOW())");
    $pn = "Accesorio Y";
    $pd = "Cargador rápido";
    $pp = 250.00;
    $ps = 5;
    $img = "y.png";
    $ip->bind_param("ssdis", $pn, $pd, $pp, $ps, $img);
    $ip->execute();
    $idProducto = $ip->insert_id;
    $ip->close();

    // Pedido
    $insPed = $cn->prepare("INSERT INTO pedidos(id_usuario, fecha_pedido, total, estado) VALUES(?, NOW(), 0, 'pendiente')");
    $insPed->bind_param("i", $idUsuario);
    $insPed->execute();
    $idPedido = $insPed->insert_id;
    $insPed->close();

    // Carrito -> detalle
    $cantidad = 3;
    $idp = $cn->prepare("INSERT INTO detalle_pedido(id_pedido, id_producto, cantidad, precio_unitario) VALUES(?, ?, ?, ?)");
    $idp->bind_param("iiid", $idPedido, $idProducto, $cantidad, $pp);
    $idp->execute();
    $idp->close();

    // Stock
    $us = $cn->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");
    $us->bind_param("ii", $cantidad, $idProducto);
    $us->execute();
    $us->close();

    // Total
    $qTot = $cn->prepare("SELECT SUM(cantidad * precio_unitario) total FROM detalle_pedido WHERE id_pedido = ?");
    $qTot->bind_param("i", $idPedido);
    $qTot->execute();
    $total = (float)$qTot->get_result()->fetch_assoc()['total'];
    $qTot->close();

    $up = $cn->prepare("UPDATE pedidos SET total = ?, estado = 'pagado' WHERE id = ?");
    $up->bind_param("di", $total, $idPedido);
    $up->execute();
    $up->close();

    // Historial del usuario (pedido debe aparecer)
    $qh = $cn->prepare("SELECT COUNT(*) c FROM pedidos WHERE id_usuario = ? AND id = ?");
    $qh->bind_param("ii", $idUsuario, $idPedido);
    $qh->execute();
    $c = (int)$qh->get_result()->fetch_assoc()['c'];
    $qh->close();
    assert_igual(1, $c, "Pedido visible en historial de usuario");

    // Comprobaciones finales
    assert_igual(750.00, $total, "Total correcto del flujo");
    $qs = $cn->prepare("SELECT stock FROM productos WHERE id = ?");
    $qs->bind_param("i", $idProducto);
    $qs->execute();
    $sRest = (int)$qs->get_result()->fetch_assoc()['stock'];
    $qs->close();
    assert_igual(2, $sRest, "Stock actualizado en flujo completo");

    $cn->rollback();
    echo "Flujo completo verificado y revertido\n";
} catch (Throwable $e) {
    $cn->rollback();
    echo "Error en flujo completo: " . $e->getMessage() . "\n";
}