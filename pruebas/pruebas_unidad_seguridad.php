<?php
require __DIR__ . "/config.php";
$cn = db();

titulo("Pruebas unitarias de seguridad");

// 1. Prevención de inyección SQL con sentencias preparadas
echo "Prueba: consulta con parámetro preparado\n";
$inputPeligroso = "' OR 1=1 --";
$stmt = $cn->prepare("SELECT id FROM productos WHERE nombre = ?");
$stmt->bind_param("s", $inputPeligroso);
$stmt->execute();
$res = $stmt->get_result();
$stmt->close();
assert_igual(0, $res->num_rows, "Entrada peligrosa no devuelve registros");

// 2. Sanitización básica de salida (simulación)
echo "Prueba: sanitización de salida HTML\n";
$entrada = "<script>alert('xss');</script>";
$salidaSegura = htmlspecialchars($entrada, ENT_QUOTES, 'UTF-8');
$contieneScript = strpos($salidaSegura, "<script>") !== false;
assert_igual(false, $contieneScript, "La salida evita ejecución de scripts");

// 3. Verificación de rol de usuario para secciones protegidas
echo "Prueba: control de acceso por rol\n";
$correo = "admin@correo.com";
$rolNecesario = "admin";

// Crear admin de prueba si no existe
$stmt = $cn->prepare("SELECT id, rol FROM usuarios WHERE correo = ?");
$stmt->bind_param("s", $correo);
$stmt->execute();
$r = $stmt->get_result();
if ($r->num_rows === 0) {
    $h = password_hash("Admin123", PASSWORD_BCRYPT);
    $rol = "admin";
    $nombre = "Admin Demo";
    $ins = $cn->prepare("INSERT INTO usuarios(nombre, correo, contrasena, rol, fecha_registro) VALUES(?, ?, ?, ?, NOW())");
    $ins->bind_param("ssss", $nombre, $correo, $h, $rol);
    $ins->execute();
    $ins->close();
}
$stmt->close();

// Verificar rol
$stmt2 = $cn->prepare("SELECT rol FROM usuarios WHERE correo = ?");
$stmt2->bind_param("s", $correo);
$stmt2->execute();
$rolDb = $stmt2->get_result()->fetch_assoc()['rol'] ?? "";
$stmt2->close();
assert_igual($rolNecesario, $rolDb, "El usuario tiene rol adecuado para la sección protegida");