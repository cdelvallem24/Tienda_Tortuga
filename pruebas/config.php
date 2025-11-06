<?php
// Configuración de conexión
$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "4988";
$DB_NAME = "tienda";

// Devuelve una conexión mysqli o finaliza el script con error claro.
function db() {
    global $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME;
    $cn = @new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    if ($cn->connect_error) {
        die("Error de conexión a la base de datos: " . $cn->connect_error);
    }
    $cn->set_charset("utf8mb4");
    return $cn;
}

// Utilidad simple para imprimir encabezados de secciones de prueba
function titulo($t) {
    echo "\n============================\n";
    echo $t . "\n";
    echo "============================\n";
}

// Utilidad de aserción simple
function assert_igual($esperado, $obtenido, $mensaje) {
    if ($esperado === $obtenido) {
        echo "[OK] " . $mensaje . " (esperado=" . var_export($esperado, true) . ", obtenido=" . var_export($obtenido, true) . ")\n";
    } else {
        echo "[FALLO] " . $mensaje . " (esperado=" . var_export($esperado, true) . ", obtenido=" . var_export($obtenido, true) . ")\n";
    }
}