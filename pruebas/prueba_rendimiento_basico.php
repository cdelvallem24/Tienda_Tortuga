<?php
require __DIR__ . "/config.php";
$cn = db();

titulo("Pruebas de rendimiento básicas");

// Medición simple de tiempo
function medir($nombre, $fn) {
    $ini = microtime(true);
    $fn();
    $fin = microtime(true);
    $t = $fin - $ini;
    echo $nombre . " -> " . round($t, 3) . " s\n";
    return $t;
}

// 1) Carga de catálogo
$t1 = medir("Carga de productos", function() use ($cn) {
    $cn->query("SELECT id, nombre, precio FROM productos LIMIT 100");
});

// 2) Búsqueda
$t2 = medir("Búsqueda de producto por nombre", function() use ($cn) {
    $stmt = $cn->prepare("SELECT id, nombre FROM productos WHERE nombre LIKE CONCAT('%', ?, '%') LIMIT 50");
    $q = "Celular";
    $stmt->bind_param("s", $q);
    $stmt->execute();
    $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
});

// 3) Confirmación de pedido simulada
$t3 = medir("Confirmación de pedido simulada", function() use ($cn) {
    $cn->begin_transaction();
    try {
        $cn->query("INSERT INTO pedidos(id_usuario, fecha_pedido, total, estado) VALUES(1, NOW(), 0, 'pendiente')");
        $idPedido = $cn->insert_id;
        $cn->query("INSERT INTO detalle_pedido(id_pedido, id_producto, cantidad, precio_unitario) VALUES($idPedido, 1, 1, 1000.00)");
        $cn->query("UPDATE productos SET stock = stock - 1 WHERE id = 1");
        $cn->query("UPDATE pedidos SET total = 1000.00, estado = 'pagado' WHERE id = $idPedido");
        $cn->commit();
    } catch (Throwable $e) {
        $cn->rollback();
    }
});

// 4) Concurrencia simple: ejecutar varias confirmaciones en bucle
$t4 = medir("Simulación de 15 transacciones en bucle", function() use ($cn) {
    for ($i = 0; $i < 15; $i++) {
        $cn->begin_transaction();
        try {
            $cn->query("INSERT INTO pedidos(id_usuario, fecha_pedido, total, estado) VALUES(1, NOW(), 0, 'pendiente')");
            $idPedido = $cn->insert_id;
            $cn->query("INSERT INTO detalle_pedido(id_pedido, id_producto, cantidad, precio_unitario) VALUES($idPedido, 1, 1, 500.00)");
            $cn->query("UPDATE productos SET stock = stock - 1 WHERE id = 1");
            $cn->query("UPDATE pedidos SET total = 500.00, estado = 'pagado' WHERE id = $idPedido");
            $cn->commit();
        } catch (Throwable $e) {
            $cn->rollback();
            echo "Error en transacción " . ($i+1) . ": " . $e->getMessage() . "\n";
        }
    }
});

echo "\nResumen de tiempos (segundos aproximados):\n";
echo "Carga de productos: " . round($t1, 3) . "\n";
echo "Búsqueda: " . round($t2, 3) . "\n";
echo "Confirmación de pedido: " . round($t3, 3) . "\n";
echo "Transacciones en bucle (15): " . round($t4, 3) . "\n";