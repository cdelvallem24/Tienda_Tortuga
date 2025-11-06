<?php
require __DIR__ . "/config.php";
$cn = db();

titulo("Prueba de integración: flujo de pedido con transacción");

try {
    $cn->begin_transaction();

    // Crear usuario temporal
    $correo = "cliente.demo@correo.com";
    $passPlano = "Cliente123";
    $hash = password_hash($passPlano, PASSWORD_BCRYPT);
    $nombre = "Cliente Demo";
    $rol = "cliente";

    // Inserta usuario
    $insU = $cn->prepare("INSERT INTO usuarios(nombre, correo, contrasena, rol, fecha_registro) VALUES(?, ?, ?, ?, NOW())");
    $insU->bind_param("ssss", $nombre, $correo, $hash, $rol);
    $insU->execute();
    $idUsuario = $insU->insert_id;
    $insU->close();

    // Crear producto temporal
    $insP = $cn->prepare("INSERT INTO productos(nombre, descripcion, precio, stock, imagen, fecha_registro) VALUES(?, ?, ?, ?, ?, NOW())");
    $pn = "Celular X";
    $pd = "4GB RAM";
    $pp = 1800.00;
    $ps = 20;
    $img = "x.png";
    $insP->bind_param("ssdis", $pn, $pd, $pp, $ps, $img);
    $insP->execute();
    $idProducto = $insP->insert_id;
    $insP->close();

    // Crear pedido
    $estado = "pendiente";
    $total = 0.00; // se calculará luego
    $insPed = $cn->prepare("INSERT INTO pedidos(id_usuario, fecha_pedido, total, estado) VALUES(?, NOW(), ?, ?)");
    $insPed->bind_param("ids", $idUsuario, $total, $estado);
    $insPed->execute();
    $idPedido = $insPed->insert_id;
    $insPed->close();

    // Agregar detalle (2 unidades)
    $cantidad = 2;
    $insDet = $cn->prepare("INSERT INTO detalle_pedido(id_pedido, id_producto, cantidad, precio_unitario) VALUES(?, ?, ?, ?)");
    $insDet->bind_param("iiid", $idPedido, $idProducto, $cantidad, $pp);
    $insDet->execute();
    $insDet->close();

    // Actualizar stock
    $updS = $cn->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");
    $updS->bind_param("ii", $cantidad, $idProducto);
    $updS->execute();
    $updS->close();

    // Recalcular total del pedido
    $qTot = $cn->prepare("SELECT SUM(cantidad * precio_unitario) AS total FROM detalle_pedido WHERE id_pedido = ?");
    $qTot->bind_param("i", $idPedido);
    $qTot->execute();
    $totalCalc = (float)$qTot->get_result()->fetch_assoc()['total'];
    $qTot->close();

    $updPed = $cn->prepare("UPDATE pedidos SET total = ?, estado = 'pagado' WHERE id = ?");
    $updPed->bind_param("di", $totalCalc, $idPedido);
    $updPed->execute();
    $updPed->close();

    // Registrar pago
    $metodo = "efectivo";
    $insPago = $cn->prepare("INSERT INTO pagos(id_pedido, metodo_pago, monto, fecha_pago) VALUES(?, ?, ?, NOW())");
    $insPago->bind_param("isd", $idPedido, $metodo, $totalCalc);
    $insPago->execute();
    $insPago->close();

    // Verificaciones de integración
    // 1) Total
    assert_igual(3600.00, $totalCalc, "Total de pedido calculado correctamente");

    // 2) Stock
    $qS = $cn->prepare("SELECT stock FROM productos WHERE id = ?");
    $qS->bind_param("i", $idProducto);
    $qS->execute();
    $stockRestante = (int)$qS->get_result()->fetch_assoc()['stock'];
    $qS->close();
    assert_igual(18, $stockRestante, "Stock actualizado correctamente");

    // 3) Pago asociado
    $qPago = $cn->prepare("SELECT COUNT(*) c FROM pagos WHERE id_pedido = ?");
    $qPago->bind_param("i", $idPedido);
    $qPago->execute();
    $cPago = (int)$qPago->get_result()->fetch_assoc()['c'];
    $qPago->close();
    assert_igual(1, $cPago, "Pago registrado y asociado al pedido");

    // Revertir todo (pruebas no dejan residuos)
    $cn->rollback();
    echo "Flujo de pedido verificado y revertido\n";
} catch (Throwable $e) {
    $cn->rollback();
    echo "Error en prueba de integración: " . $e->getMessage() . "\n";
}