<?php
// Conexión a la base de datos
$host = "localhost";
$dbname = "tienda";
$user = "root";
$pass = "4988";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Iniciar sesión
session_start();
$errores = [];

// Si ya está logueado
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!$usuario || !$password) {
        $errores[] = "Todos los campos son obligatorios.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :user OR email = :user LIMIT 1");
        $stmt->execute([':user' => $usuario]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['rol'] = $user['rol'];
            header("Location: index.php");
            exit;
        } else {
            $errores[] = "Usuario o contraseña incorrectos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Login - Tienda</title>
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <style>
        body {
            background: #f5f7fa;
            padding-top: 60px;
        }

        .panel-login {
            max-width: 400px;
            margin: 0 auto;
        }

        .panel-footer {
            text-align: center;
            padding: 10px;
            font-size: 14px;
        }

        .error {
            background: #f2dede;
            color: #a94442;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
<div class="container">
    <div class="panel panel-default panel-login">
        <div class="panel-heading text-center">
            <h3>Iniciar Sesión</h3>
        </div>
        <div class="panel-body">
            <?php if ($errores): ?>
                <div class="error">
                    <?php foreach ($errores as $e): ?>
                        <p><?php echo htmlspecialchars($e); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label for="usuario">Usuario o Email</label>
                    <input type="text" name="usuario" class="form-control" required placeholder="usuario o correo">
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" name="password" class="form-control" required placeholder="contraseña">
                </div>

                <button type="submit" class="btn btn-primary btn-block">Entrar</button>
            </form>
        </div>
        <div class="panel-footer">
            ¿No tienes cuenta? <a href="registro.php">Crear una aquí</a>
        </div>
    </div>
</div>
</body>
</html>
