<?php
session_start();
include 'Configuracion.php';

// Capturar texto de búsqueda si existe
$buscar = "";
if (isset($_GET['buscar']) && !empty(trim($_GET['buscar']))) {
    $buscar = $db->real_escape_string(trim($_GET['buscar']));
    $query = $db->query("SELECT * FROM mis_productos WHERE name LIKE '%$buscar%' ORDER BY id DESC");
} else {
    $query = $db->query("SELECT * FROM mis_productos ORDER BY id DESC LIMIT 10");
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title>Carrito de Compras</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <style>
    .container {
        padding: 20px;
    }

    .cart-link {
        width: 100%;
        text-align: right;
        display: block;
        font-size: 22px;
    }

    .search-box {
        margin: 20px 0;
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
                    <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                    <li role="presentation"><a href="insertar_producto.php">Agregar Producto</a></li>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                    <li role="presentation"><a href="ventas.php">Ventas</a></li>
                    <?php endif; ?>

                    <li role="presentation"><a href="logout.php">Logout</a></li>
                </ul>
                <h3 style="margin: 0;">Tienda Tortuga 🐢</h3>
            </div>

            <div class="panel-body">
                <h1>Tienda de Productos</h1>

                <form method="GET" class="search-box">
                    <div class="input-group">
                        <input type="text" name="buscar" class="form-control" placeholder="Buscar producto..."
                            value="<?php echo htmlspecialchars($buscar); ?>">
                        <span class="input-group-btn">
                            <button class="btn btn-primary" type="submit">Buscar</button>
                        </span>
                    </div>
                </form>

                <a href="VerCarta.php" class="cart-link" title="Ver Carta">
                    <i class="glyphicon glyphicon-shopping-cart"></i>
                </a>

                <div id="products" class="row list-group">
                    <?php
                if ($query->num_rows > 0) {
                    while ($row = $query->fetch_assoc()) {
                ?>
                    <div class="item col-lg-4">
                        <div class="thumbnail">
                            <img src="http://127.0.0.1/carrito/imagenes/<?php echo $row['imagen']; ?>"
                                class="img-responsive"
                                style="height: 200px; width: 100%; object-fit: contain; background-color: #fff;"
                                alt="<?php echo $row['name']; ?>">

                            <div class="caption">
                                <h4 class="list-group-item-heading"><?php echo $row["name"]; ?></h4>
                                <p class="list-group-item-text">
                                    <?php echo $row["description"]; ?>
                                </p>
                                <p class="text-muted">
                                    Stock disponible: <?php echo $row["stock"]; ?>
                                </p>

                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="lead"><?php echo 'Q' . $row["price"]; ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <?php if ($row["stock"] > 0): ?>
                                        <a class="btn btn-success"
                                            href="AccionCarta.php?action=addToCart&id=<?php echo $row["id"]; ?>">
                                            Enviar al Carrito
                                        </a>
                                        <?php else: ?>
                                        <button class="btn btn-danger" disabled>No hay stock</button>
                                        <?php endif; ?>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    }
                } else {
                    echo "<p>No se encontraron productos.</p>";
                }
                ?>
                </div>
            </div>
        </div>
        <div class="panel-footer text-center">
            <a href="terminos.php" target="_blank">Términos y Condiciones</a> |
            <a href="privacidad.php" target="_blank">Política de Privacidad</a>
        </div>

    </div>
</body>

</html>