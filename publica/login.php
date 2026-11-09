<?php
session_start();
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST['usuario'] ?? '');
    $clave = trim($_POST['clave'] ?? '');

    if (!empty($usuario) && !empty($clave)) {
        try {
            // Ajusta la ruta de la base de datos según la ubicación real de CURSADAS.db
            $db_path = __DIR__ . '/../CURSADAS.db'; 
            $pdo = new PDO("sqlite:" . $db_path);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Consulta segura con sentencia preparada para evitar SQL Injection
            $sql = "SELECT * FROM T_USUARIOS WHERE USUARIO = :usuario AND CLAVE = :clave";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':usuario' => $usuario,
                ':clave' => $clave // Nota: Si en tu BD usas hashes (password_hash), deberías verificar con password_verify()
            ]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Credenciales válidas: creamos las variables de sesión necesarias
                $_SESSION['usuario_autenticado'] = $user['USUARIO'];
                
                // Guardamos el perfil obtenido de la base de datos (asegúrate de que el campo en tu tabla se llame 'PERFIL')
                $_SESSION['perfil'] = isset($user['PERFIL']) ? $user['PERFIL'] : 1;
                
                // Guardamos el nombre del usuario para mostrarlo en el panel (ajusta 'NOMBRE' por el nombre real de la columna en tu tabla)
                $_SESSION['nombre'] = isset($user['NOMBRE']) ? $user['NOMBRE'] : $user['USUARIO'];
                
                // Redirigimos a index.php
                header("Location: index.php");
                exit();
            } else {
                $mensaje = "Usuario o contraseña incorrectos.";
            }

        } catch (PDOException $e) {
            $mensaje = "Error de conexión: " . $e->getMessage();
        }
    } else {
        $mensaje = "Por favor, completa ambos campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f4f4f9; }
        .login-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 300px; }
        .login-box h2 { margin-bottom: 20px; text-align: center; color: #333; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-size: 14px; color: #666; }
        .form-group input { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        .btn { height: 50%; width: 100%; padding: 10px; background: #007bff; border: none; color: white; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .btn:hover { background: #0056b3; }
        .error { color: #dc3545; font-size: 14px; text-align: center; margin-bottom: 10px; }
    </style>
    <link rel="stylesheet" href="./estilos.css">
</head>
<body>
    
    <div class="login-box">
        <h1>Platafarma de Gestión de Cursadas</h1><br>
        <img src="./logo.png" width="100%" header="100%" alt="Logo JPR software" class="login-logo">
        <h2>Acceso al Sistema</h2>
        <?php if (!empty($mensaje)): ?>
            <div class="error"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="usuario">Usuario:</label>
                <input type="text" id="usuario" name="usuario" required autocomplete="off">
            </div>
            <div class="form-group">
                <label for="clave">Contraseña:</label>
                <input type="password" id="clave" name="clave" required>
            </div>
            <button type="submit" class="btn">Entrar</button>
        </form>
    </div>
</body>
</html>