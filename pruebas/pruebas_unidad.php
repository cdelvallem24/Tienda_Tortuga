<?php
require __DIR__ . "/config.php";
$cn = db();

titulo("Pruebas unitarias básicas");

// 1. Conexión a BD
echo "Prueba: conexión a BD\n";
$res = $cn->query("SELECT 1 AS ok");
assert_igual(1, (int)$res->fetch_assoc()['ok'], "Conexión operativa");

// 2. Hash de contraseña
echo "Prueba: hash de contraseña\n";
$clavePlano = "Secreto123";
$hash = password_hash($clavePlano, PASSWORD_BCRYPT);
$verifica = password_verify("Secreto123", $hash);
assert_igual(true, $verifica, "password_hash y password_verify funcionan");

// 3. Login con sentencia preparada (ajusta nombre de columna contrasena/contraseña)
echo "Prueba: login con credenciales válidas\n";
$correoDemo = "demo@correo.com";
$passDemoPlano = "Secreto123";

// Inserta usuario de prueba si no existe
$stmt = $cn->prepare("SELECT id, contrasena FROM usuarios WHERE correo = ?");
$stmt->bind_param("s", $correoDemo);
$stmt->execute();
$rs = $stmt->get_result();
if ($rs->num_rows === 0) {
    $hashDemo = password_hash($passDemoPlano, PASSWORD_BCRYPT);
    $rol = "cliente";
    $stmtIns = $cn->prepare("INSERT INTO usuarios(nombre, correo, contrasena, rol, fecha_registro) VALUES(?, ?, ?, ?, NOW())");
    $nombre = "Usuario Demo";
    $stmtIns->bind_param("ssss", $nombre, $correoDemo, $hashDemo, $rol);
    $stmtIns->execute();
    $stmtIns->close();
}
$stmt->close();

$stmtLogin = $cn->prepare("SELECT id, contrasena FROM usuarios WHERE correo = ?");
$stmtLogin->bind_param("s", $correoDemo);
$stmtLogin->execute();
$u = $stmtLogin->get_result()->fetch_assoc();
$stmtLogin->close();
$okLogin = password_verify($passDemoPlano, $u['contrasena']);
assert_igual(true, $okLogin, "Login verifica hash correctamente");

// 4. CRUD mínimo de producto con sentencias preparadas
echo "Prueba: CRUD básico de producto\n";
$cn->begin_transaction();
try {
    // Crear
    $stmtC = $cn->prepare("INSERT INTO productos(nombre, descripcion, precio, stock, imagen, fecha_registro) VALUES(?, ?, ?, ?, ?, NOW())");
    $pn = "Producto Prueba";
    $pd = "Descripción";
    $pp = 1999.50;
    $ps = 10;
    $pi = "img.png";
    $stmtC->bind_param("ssdis", $pn, $pd, $pp, $ps, $pi);
    $stmtC->execute();
    $idProd = $stmtC->insert_id;
    $stmtC->close();

    // Leer
    $stmtR = $cn->prepare("SELECT nombre, precio, stock FROM productos WHERE id = ?");
    $stmtR->bind_param("i", $idProd);
    $stmtR->execute();
    $row = $stmtR->get_result()->fetch_assoc();
    $stmtR->close();
    assert_igual($pn, $row['nombre'], "Producto creado con nombre correcto");

    // Actualizar
    $stmtU = $cn->prepare("UPDATE productos SET stock = stock + 5 WHERE id = ?");
    $stmtU->bind_param("i", $idProd);
    $stmtU->execute();
    $stmtU->close();

    $stmtR2 = $cn->prepare("SELECT stock FROM productos WHERE id = ?");
    $stmtR2->bind_param("i", $idProd);
    $stmtR2->execute();
    $row2 = $stmtR2->get_result()->fetch_assoc();
    $stmtR2->close();
    assert_igual(15, (int)$row2['stock'], "Actualización de stock suma correctamente");

    // Eliminar
    $stmtD = $cn->prepare("DELETE FROM productos WHERE id = ?");
    $stmtD->bind_param("i", $idProd);
    $stmtD->execute();
    $stmtD->close();

    $cn->rollback(); // No dejar residuos de prueba
    echo "CRUD de producto validado y revertido\n";
} catch (Throwable $e) {
    $cn->rollback();
    echo "Error en CRUD de producto: " . $e->getMessage() . "\n";
}

// 5. Cálculo de total del carrito (unitario puro)
echo "Prueba: cálculo de total del carrito\n";
$carrito = [
    ["precio" => 1200.00, "cantidad" => 2],
    ["precio" => 850.50, "cantidad" => 1],
];
$total = 0;
foreach ($carrito as $it) {
    $total += ($it["precio"] * $it["cantidad"]);
}
assert_igual(3250.50, $total, "Total de carrito calculado correctamente");