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

$errores = [];
$exito = "";

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $direccion = trim($_POST['direccion'] ?? '');
    $celular = trim($_POST['celular'] ?? '');

    // Validaciones
    if (!$username || !$email || !$password || !$confirm_password || !$direccion || !$celular) {
        $errores[] = "Todos los campos son obligatorios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "Correo inválido.";
    } elseif ($password !== $confirm_password) {
        $errores[] = "Las contraseñas no coinciden.";
    } else {
        // Verificar si ya existe el usuario
        $check = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check->execute([$username, $email]);

        if ($check->fetch()) {
            $errores[] = "El usuario o email ya existe.";
        } else {
            // Insertar usuario
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, direccion, celular) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$username, $email, $password_hash, $direccion, $celular]);
            $exito = "Usuario registrado con éxito. Ya puedes iniciar sesión.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <style>
        body {
            background: #f5f7fa;
            padding-top: 50px;
        }

        .panel-register {
            max-width: 500px;
            margin: 0 auto;
        }

        .error {
            background: #f2dede;
            color: #a94442;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .success {
            background: #dff0d8;
            color: #3c763d;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        h3 {
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="panel panel-default panel-register">
        <div class="panel-heading">
            <h3>Crear Cuenta</h3>
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
                    <label>Nombre de Usuario</label>
                    <input type="text" name="username" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Correo Electrónico</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Contraseña</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Confirmar Contraseña</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Celular</label>
                    <input type="text" name="celular" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Dirección</label>
                    <textarea name="direccion" class="form-control" required rows="3"></textarea>
                </div>

                <button type="submit" class="btn btn-success btn-block">Registrar</button>
            </form>
        </div>
        <div class="panel-footer text-center">
            ¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>
        </div>
    </div>
</div>
</body>
</html>
