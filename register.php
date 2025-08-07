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
$success_message = "";

// Procesar el formulario si se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Obtener conexión a la base de datos
        $conn = getDBConnection();
        
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Validar datos
        $errors = [];
        
        if (strlen($password) < 8) {
            $errors[] = "La contraseña debe tener al menos 8 caracteres";
        }
        
        if (!preg_match("/[A-Z]/", $password)) {
            $errors[] = "La contraseña debe incluir al menos una letra mayúscula";
        }
        
        if (!preg_match("/[a-z]/", $password)) {
            $errors[] = "La contraseña debe incluir al menos una letra minúscula";
        }
        
        if (!preg_match("/[0-9]/", $password)) {
            $errors[] = "La contraseña debe incluir al menos un número";
        }
        
        if (!preg_match("/[\W]/", $password)) {
            $errors[] = "La contraseña debe incluir al menos un carácter especial";
        }
        
        if ($password !== $confirm_password) {
            $errors[] = "Las contraseñas no coinciden";
        }
        
        // Verificar si el email ya existe
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$email]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($result) > 0) {
            $errors[] = "El correo electrónico ya está registrado";
        }
        
        if (empty($errors)) {
            // Encriptar contraseña
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insertar usuario
            $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            
            if ($stmt->execute([$name, $email, $hashed_password])) {
                $success_message = "Registro exitoso. Ahora puede iniciar sesión.";
            } else {
                $error_message = "Error al registrar usuario.";
            }
        } else {
            $error_message = implode("<br>", $errors);
        }
    } catch (PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - WiFi Scanner</title>
    <!-- Tailwind CSS desde CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h1 class="text-2xl font-bold mb-2 text-center">Crear Cuenta</h1>
        <p class="text-gray-600 mb-6 text-center">Complete el formulario para registrarse</p>
        
        <?php if (!empty($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $error_message; ?></span>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success_message)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $success_message; ?></span>
                <p class="mt-2">
                    <a href="login.php" class="text-blue-600 hover:underline">Ir a la página de inicio de sesión</a>
                </p>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-medium mb-2">Nombre Completo</label>
                <input type="text" id="name" name="name" placeholder="Ingrese su nombre" required
                       class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-medium mb-2">Correo Electrónico</label>
                <input type="email" id="email" name="email" placeholder="ejemplo@gmail.com" required
                       class="w-full px-4 py-2 bg-blue-50 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="mb-4">
                <label for="password" class="block text-gray-700 font-medium mb-2">Contraseña</label>
                <input type="password" id="password" name="password" required
                       class="w-full px-4 py-2 bg-blue-50 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-sm text-gray-600 mt-1">
                    Mínimo 8 caracteres, incluyendo mayúsculas, minúsculas, números y caracteres especiales.
                </p>
            </div>
            
            <div class="mb-6">
                <label for="confirm_password" class="block text-gray-700 font-medium mb-2">Confirmar Contraseña</label>
                <input type="password" id="confirm_password" name="confirm_password" required
                       class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <button type="submit" class="w-full bg-black text-white py-2 px-4 rounded-md hover:bg-gray-800 transition duration-300 mb-6">
                Registrarse
            </button>
            
            <div class="text-center">
                <p class="text-gray-600">¿Ya tiene una cuenta? <a href="login.php" class="text-blue-600 hover:underline">Iniciar Sesión</a></p>
            </div>
        </form>
    </div>
</body>
</html>
