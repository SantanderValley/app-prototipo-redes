<?php
// Incluir la configuración de la base de datos SQLite
require_once 'db_config.php';

// Iniciar sesión
session_start();

// Verificar si ya está logueado
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error_message = "";

// Procesar el formulario si se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Obtener conexión a la base de datos SQLite
        $db = getDBConnection();
        
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        // Verificar credenciales usando PDO para SQLite
        $stmt = $db->prepare("SELECT user_id, name, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Verificar contraseña
            if (password_verify($password, $user['password'])) {
                // Login exitoso
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['name'];
                
                // Actualizar la fecha de último login en la base de datos
                $update_stmt = $db->prepare("UPDATE users SET last_login = datetime('now') WHERE user_id = ?");
                $update_stmt->execute([$user['user_id']]);
                
                // Registrar intento exitoso de login
                $ip_address = $_SERVER['REMOTE_ADDR'];
                $login_stmt = $db->prepare("INSERT INTO login_attempts (email, ip_address, success) VALUES (?, ?, 1)");
                $login_stmt->execute([$email, $ip_address]);
                
                // Verificar si hay una redirección pendiente
                if (isset($_SESSION['redirect_after_login']) && !empty($_SESSION['redirect_after_login'])) {
                    $redirect = $_SESSION['redirect_after_login'];
                    unset($_SESSION['redirect_after_login']); // Limpiar la variable de sesión
                    header("Location: $redirect");
                } else {
                    header("Location: dashboard.php");
                }
                exit;
            } else {
                $error_message = "Contraseña incorrecta";
                
                // Registrar intento fallido
                $ip_address = $_SERVER['REMOTE_ADDR'];
                $login_stmt = $db->prepare("INSERT INTO login_attempts (email, ip_address, success) VALUES (?, ?, 0)");
                $login_stmt->execute([$email, $ip_address]);
            }
        } else {
            $error_message = "Usuario no encontrado";
            
            // Registrar intento fallido con usuario inexistente
            if (!empty($email)) {
                $ip_address = $_SERVER['REMOTE_ADDR'];
                $login_stmt = $db->prepare("INSERT INTO login_attempts (email, ip_address, success) VALUES (?, ?, 0)");
                $login_stmt->execute([$email, $ip_address]);
            }
        }
    } catch (PDOException $e) {
        $error_message = "Error en el sistema: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - WiFi Scanner</title>
    <!-- Tailwind CSS desde CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h1 class="text-2xl font-bold mb-2 text-center">Iniciar Sesión</h1>
        <p class="text-gray-600 mb-6 text-center">Ingrese sus credenciales para acceder a su cuenta</p>
        
        <?php if (!empty($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $error_message; ?></span>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-medium mb-2">Correo Electrónico</label>
                <input type="email" id="email" name="email" placeholder="nombre@ejemplo.com" required
                       class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="mb-6">
                <div class="flex justify-between items-center mb-2">
                    <label for="password" class="block text-gray-700 font-medium">Contraseña</label>
                    <a href="forgot_password.php" class="text-sm text-blue-600 hover:underline">¿Olvidó su contraseña?</a>
                </div>
                <input type="password" id="password" name="password" required
                       class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <button type="submit" class="w-full bg-black text-white py-2 px-4 rounded-md hover:bg-gray-800 transition duration-300 mb-6">
                Iniciar Sesión
            </button>
            
            <div class="text-center">
                <p class="text-gray-600">¿No tiene una cuenta? <a href="register.php" class="text-blue-600 hover:underline">Registrarse</a></p>
            </div>
        </form>
    </div>
</body>
</html>
