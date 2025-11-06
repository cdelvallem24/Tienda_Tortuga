<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

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

$user_id = $_SESSION['user_id'];
$errores = [];
$exito = "";

// Obtener datos actuales del usuario
$stmt = $pdo->prepare("SELECT username, email, direccion, celular FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    die("Usuario no encontrado.");
}

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_username = trim($_POST['username'] ?? '');
    $nuevo_email = trim($_POST['email'] ?? '');
    $nueva_direccion = trim($_POST['direccion'] ?? '');
    $nuevo_password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $nuevo_celular = trim($_POST['celular'] ?? '');

    // Validaciones
    if (!$nuevo_username || !$nuevo_email || !$nueva_direccion  || !$nuevo_celular) {
        $errores[] = "Todos los campos son obligatorios.";
    } elseif (!filter_var($nuevo_email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "Correo electrónico inválido.";
    }

    // Verificar duplicados (de otros usuarios)
    $check = $pdo->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
    $check->execute([$nuevo_username, $nuevo_email, $user_id]);
    if ($check->fetch()) {
        $errores[] = "El usuario o correo ya están en uso por otro usuario.";
    }

    // Si no hay errores, actualizar
    if (empty($errores)) {
        if ($nuevo_password) {
            if ($nuevo_password !== $confirm_password) {
                $errores[] = "Las contraseñas no coinciden.";
            } else {
                $passwordHash = password_hash($nuevo_password, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET username = ?, email = ?, direccion = ?, celular = ?, password = ? WHERE id = ?";
                $pdo->prepare($sql)->execute([$nuevo_username, $nuevo_email, $nueva_direccion, $nuevo_celular, $passwordHash, $user_id]);
                $exito = "Datos actualizados y contraseña cambiada.";
            }
        } else {
            $sql = "UPDATE users SET username = ?, email = ?, direccion = ?, celular = ? WHERE id = ?";
            $pdo->prepare($sql)->execute([$nuevo_username, $nuevo_email, $nueva_direccion, $nuevo_celular, $user_id]);
            $exito = "Datos actualizados con éxito.";
        }

        // Actualizar sesión si el username o email cambiaron
        $_SESSION['username'] = $nuevo_username;
        $_SESSION['email'] = $nuevo_email;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <style>
        body {
            padding-top: 50px;
            background: #f5f5f5;
        }

        .panel {
            max-width: 600px;
            margin: auto;
        }

        .error {
            background: #f2dede;
            color: #a94442;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .success {
            background: #dff0d8;
            color: #3c763d;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading text-center">
            <h3>Editar Perfil</h3>
        </div>
        <div class="panel-body">
            <?php if ($errores): ?>
                <div class="error">
                    <?php foreach ($errores as $e): ?>
                        <p><?php echo htmlspecialchars($e); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($exito): ?>
                <div class="success"><?php echo htmlspecialchars($exito); ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label>Nombre de usuario</label>
                    <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($usuario['username']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Correo electrónico</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Dirección</label>
                    <textarea name="direccion" class="form-control" rows="3" required><?php echo htmlspecialchars($usuario['direccion']); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Celular</label>
                    <input type="text" name="celular" class="form-control" 
                    value="<?php echo htmlspecialchars($usuario['celular']); ?>" required>
                </div>

                <hr>
                <h4>Cambiar contraseña (opcional)</h4>

                <div class="form-group">
                    <label>Nueva contraseña</label>
                    <input type="password" name="password" class="form-control">
                </div>

                <div class="form-group">
                    <label>Confirmar nueva contraseña</label>
                    <input type="password" name="confirm_password" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary btn-block">Guardar cambios</button>
            </form>
        </div>
        <div class="panel-footer text-center">
            <a href="index.php">Volver al panel</a> | <a href="logout.php">Cerrar sesión</a>
        </div>
    </div>
</div>
</body>
</html>
