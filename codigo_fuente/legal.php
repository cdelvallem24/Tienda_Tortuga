<?php
include 'Configuracion.php';
$slug = $_GET['pagina'] ?? '';
$query = $db->prepare("SELECT * FROM paginas_legales WHERE slug = ?");
$query->bind_param("s", $slug);
$query->execute();
$result = $query->get_result();
$page = $result->fetch_assoc();

if (!$page) {
    header("HTTP/1.0 404 Not Found");
    echo "Página no encontrada";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($page['titulo']); ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>
<div class="container" style="max-width: 900px; margin-top: 30px;">
    <h1><?php echo htmlspecialchars($page['titulo']); ?></h1>
    <div><?php echo nl2br($page['contenido']); ?></div>
    <a href="index.php" class="btn btn-primary" style="margin-top: 20px;">Volver al inicio</a>
</div>
</body>
</html>
