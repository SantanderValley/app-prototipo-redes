<?php
// Incluir el archivo de verificación de autenticación
require_once 'check_auth.php';

// Verificar que el usuario esté autenticado
require_auth();

// Incluir archivo de configuración de base de datos SQLite
require_once 'db_config.php';

// Conectar a la base de datos SQLite
try {
    $conn = getDBConnection();
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

// Verificar que se proporciona un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$scan_id = (int)$_GET['id'];

// Obtener información del escaneo
$stmt = $conn->prepare("SELECT * FROM scans WHERE scan_id = ?");
$stmt->execute([$scan_id]);
$scan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$scan) {
    header("Location: dashboard.php");
    exit;
}

// Obtener información de redes WiFi
$networks_stmt = $conn->prepare("SELECT * FROM wifi_networks WHERE scan_id = ?");
$networks_stmt->execute([$scan_id]);
$all_networks = $networks_stmt->fetchAll(PDO::FETCH_ASSOC);
$networks_count = count($all_networks);

// Obtener dispositivos
$devices_stmt = $conn->prepare("SELECT * FROM devices WHERE scan_id = ?");
$devices_stmt->execute([$scan_id]);
$all_devices = $devices_stmt->fetchAll(PDO::FETCH_ASSOC);
$devices_count = count($all_devices);

// Obtener vulnerabilidades
$vulnerabilities_stmt = $conn->prepare("SELECT * FROM vulnerabilities WHERE scan_id = ?");
$vulnerabilities_stmt->execute([$scan_id]);
$all_vulnerabilities = $vulnerabilities_stmt->fetchAll(PDO::FETCH_ASSOC);
$vulnerabilities_count = count($all_vulnerabilities);

// Obtener recomendaciones
$recommendations_stmt = $conn->prepare("SELECT * FROM ai_recommendations WHERE scan_id = ?");
$recommendations_stmt->execute([$scan_id]);
$all_recommendations = $recommendations_stmt->fetchAll(PDO::FETCH_ASSOC);
$recommendations_count = count($all_recommendations);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Escaneo #<?= $scan_id ?> - WiFi Scanner</title>
    <!-- Tailwind CSS desde CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Iconos Remix -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 flex overflow-hidden h-screen">
    <!-- Sidebar -->
    <aside class="flex flex-col w-64 h-screen px-4 py-8 overflow-y-auto bg-white border-r rtl:border-r-0 rtl:border-l shadow-md fixed">
        <!-- Logo -->
        <div class="flex items-center space-x-2">
            <i class="ri-wifi-fill text-blue-600 text-2xl"></i>
            <span class="text-xl font-bold text-gray-800">EscanerRedes</span>
        </div>

        <div class="flex flex-col justify-between flex-1 mt-6">
            <!-- Navegación -->
            <nav>
                <a class="flex items-center px-4 py-2 text-gray-600 transition-colors duration-300 transform rounded-md hover:bg-gray-100 hover:text-gray-700" href="dashboard.php">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 11H5M19 11C20.1046 11 21 11.8954 21 13V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V13C3 11.8954 3.89543 11 5 11M19 11V9C19 7.89543 18.1046 7 17 7M5 11V9C5 7.89543 5.89543 7 7 7M7 7V5C7 3.89543 7.89543 3 9 3H15C16.1046 3 17 3.89543 17 5V7M7 7H17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span class="mx-4 font-medium">Dashboard</span>
                </a>
            </nav>

            <!-- Usuario y Logout -->
            <div class="mt-6">
                <hr class="my-6 border-gray-200" />
                <div class="flex items-center justify-between px-4">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="font-medium text-gray-700"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuario'); ?></span>
                    </div>
                    <a href="logout.php" class="text-gray-600 hover:text-red-600 transition-colors duration-300">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                            <path d="M19 21H10C8.89543 21 8 20.1046 8 19V15H10V19H19V5H10V9H8V5C8 3.89543 8.89543 3 10 3H19C20.1046 3 21 3.89543 21 5V19C21 20.1046 20.1046 21 19 21ZM12 16V13H3V11H12V8L17 12L12 16Z" fill="currentColor"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </aside>

    <!-- Contenido principal -->
    <div class="flex-1 p-6 overflow-auto ml-64 h-screen">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Detalles del Escaneo #<?= $scan_id ?></h1>
                <p class="text-sm text-gray-600">Realizado el <?= date('d/m/Y H:i', strtotime($scan['scan_date'])) ?> (Duración: <?= round($scan['scan_duration'], 2) ?> segundos)</p>
            </div>
            <div>
                <a href="dashboard.php" class="flex items-center text-blue-600 hover:text-blue-800">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path></svg>
                    Volver al Dashboard
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Resumen del Escaneo</h2>
                <a href="export_pdf.php?id=<?= $scan_id ?>" class="flex items-center text-green-600 hover:text-green-800" target="_blank">
                    <i class="ri-file-pdf-line mr-1"></i>
                    Exportar PDF
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-4 border rounded-lg text-center">
                    <div class="text-4xl font-bold text-blue-600 mb-2"><?= $scan['total_devices'] ?></div>
                    <div class="text-sm text-gray-600">Dispositivos detectados</div>
                </div>
                <div class="p-4 border rounded-lg text-center">
                    <div class="text-4xl font-bold <?= $scan['total_vulnerabilities'] > 0 ? 'text-red-600' : 'text-green-600' ?> mb-2"><?= $scan['total_vulnerabilities'] ?></div>
                    <div class="text-sm text-gray-600">Vulnerabilidades</div>
                </div>
                <div class="p-4 border rounded-lg text-center">
                    <div class="text-4xl font-bold text-purple-600 mb-2"><?= $networks_count ?></div>
                    <div class="text-sm text-gray-600">Redes Wi-Fi</div>
                </div>
            </div>
        </div>

        <!-- Información de Redes WiFi -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="p-4 border-b">
                <h2 class="text-xl font-semibold text-gray-800">Redes WiFi Detectadas</h2>
            </div>
            <div class="p-4">
                <?php if ($networks_count > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SSID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Autenticación</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cifrado</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Señal</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clave de Seguridad</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach($all_networks as $network): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $network['ssid'] ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $network['authentication'] ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $network['encryption'] ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $network['signal'] ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $network['security_key'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="p-4 text-center text-gray-500 bg-gray-50 rounded">No se detectaron redes WiFi.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Información de Dispositivos -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="p-4 border-b">
                <h2 class="text-xl font-semibold text-gray-800">Dispositivos Detectados</h2>
            </div>
            <div class="p-4">
                <?php if ($devices_count > 0): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php foreach($all_devices as $device): ?>
                            <div class="border rounded-lg overflow-hidden">
                                <div class="bg-blue-50 p-4 border-b">
                                    <h3 class="font-semibold text-gray-800">
                                        <?= $device['hostname'] != 'Unknown' ? $device['hostname'] : 'Dispositivo sin nombre' ?>
                                    </h3>
                                </div>
                                <div class="p-4">
                                    <div class="mb-2"><span class="font-semibold">IP:</span> <?= $device['ip_address'] ?></div>
                                    <div class="mb-4"><span class="font-semibold">Visto por última vez:</span> <?= $device['last_seen'] ?></div>
                                    
                                    <!-- Puertos abiertos -->
                                    <?php
                                    $device_id = $device['device_id'];
                                    $ports_stmt = $conn->prepare("SELECT * FROM open_ports WHERE device_id = ?");
                                    $ports_stmt->execute([$device_id]);
                                    $all_ports = $ports_stmt->fetchAll(PDO::FETCH_ASSOC);
                                    $ports_count = count($all_ports);
                                    
                                    if (!empty($all_ports)): 
                                    ?>
                                        <h4 class="font-semibold text-gray-700 mb-2">Puertos abiertos:</h4>
                                        <ul class="divide-y divide-gray-200 border rounded-md">
                                            <?php foreach($all_ports as $port): ?>
                                                <li class="p-3 flex justify-between items-center hover:bg-gray-50">
                                                    <span>
                                                        <span class="font-medium">Puerto <?= $port['port_number'] ?></span>
                                                        <span class="text-gray-500 ml-2">(<?= $port['service_name'] ?>)</span>
                                                    </span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="p-4 text-center text-gray-500 bg-gray-50 rounded">No se detectaron dispositivos.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Vulnerabilidades -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="p-4 border-b">
                <h2 class="text-xl font-semibold text-gray-800">Vulnerabilidades Detectadas</h2>
            </div>
            <div class="p-4">
                <?php if ($vulnerabilities_count > 0): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php foreach($all_vulnerabilities as $vulnerability): ?>
                            <?php 
                            $headerClass = '';
                            $badgeClass = '';
                            $badgeBg = '';
                            
                            switch(strtolower($vulnerability['severity'])) {
                                case 'high': 
                                    $headerClass = 'bg-red-100 border-red-200'; 
                                    $badgeClass = 'bg-red-600 text-white'; 
                                    break;
                                case 'medium': 
                                    $headerClass = 'bg-yellow-100 border-yellow-200'; 
                                    $badgeClass = 'bg-yellow-500 text-white'; 
                                    break;
                                case 'low': 
                                    $headerClass = 'bg-blue-100 border-blue-200'; 
                                    $badgeClass = 'bg-blue-600 text-white'; 
                                    break;
                                default: 
                                    $headerClass = 'bg-gray-100 border-gray-200'; 
                                    $badgeClass = 'bg-gray-600 text-white';
                            }
                            
                            // Determinar si necesitamos mostrar una recomendación automática basada en el tipo
                            $auto_recommendation = null;
                            switch(strtolower($vulnerability['vuln_type'])) {
                                case 'password':
                                    $auto_recommendation = 'Utilice contraseñas fuertes con al menos 12 caracteres, combinando letras mayúsculas, minúsculas, números y símbolos. Cambie sus contraseñas periódicamente y considere usar un gestor de contraseñas.';
                                    break;
                                case 'authentication':
                                    $auto_recommendation = 'Implemente métodos de autenticación robustos como WPA3 para redes WiFi o autenticación de dos factores (2FA) para servicios. Evite métodos de autorización obsoletos.';
                                    break;
                                case 'open_port':
                                    $auto_recommendation = 'Cierre los puertos innecesarios o configure su firewall para restringir el acceso solo a direcciones IP confiables. Mantenga actualizados los servicios que se ejecutan en los puertos abiertos.';
                                    break;
                            }
                            ?>
                            <div class="border rounded-lg overflow-hidden">
                                <div class="p-4 border-b flex justify-between items-center <?= $headerClass ?>">
                                    <h3 class="font-semibold text-gray-800"><?= $vulnerability['vuln_type'] ?></h3>
                                    <span class="px-2 py-1 text-xs rounded-full font-semibold <?= $badgeClass ?>">
                                        <?= $vulnerability['severity'] ?? 'N/A' ?>
                                    </span>
                                </div>
                                <div class="p-4">
                                    <p class="text-gray-800 mb-4"><?= $vulnerability['description'] ?></p>
                                    
                                    <?php if (!empty($vulnerability['details'])): ?>
                                        <div class="mb-4 text-sm bg-gray-50 p-3 rounded border border-gray-200">
                                            <div class="font-medium text-gray-700 mb-1">Detalles adicionales:</div>
                                            <div class="text-gray-600"><?= $vulnerability['details'] ?></div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($vulnerability['affected_device'])): ?>
                                        <div class="mb-4">
                                            <span class="font-medium text-gray-700">Dispositivo afectado:</span> 
                                            <span class="text-gray-800"><?= $vulnerability['affected_device'] ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <div class="font-medium text-gray-700 mb-2">
                                            Recomendación:
                                            <?php 
                                            if (isset($vulnerability['priority']) && $vulnerability['priority'] == 'high') {
                                                echo '<span class="ml-2 px-2 py-0.5 text-xs bg-red-100 text-red-800 rounded-full">Prioritaria</span>';
                                            }
                                            ?>
                                        </div>
                                        <p class="text-gray-600"><?= isset($auto_recommendation) ? $auto_recommendation : ($vulnerability['recommendation'] ?? 'Actualice el firmware del dispositivo a la última versión y consulte con el fabricante sobre parches de seguridad disponibles.') ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="p-4 text-center text-green-700 bg-green-50 rounded border border-green-200">
                        <i class="fas fa-check-circle mr-2"></i> No se detectaron vulnerabilidades.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recomendaciones de seguridad -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="p-4 border-b">
                <h2 class="text-xl font-semibold text-gray-800">Recomendaciones de Seguridad</h2>
            </div>
            <div class="p-4">
                <?php if ($recommendations_count > 0): ?>
                    <div class="divide-y divide-gray-200">
                        <?php foreach($all_recommendations as $recommendation): 
                            // Asignar un valor predeterminado a 'priority' si no existe
                            $priority = isset($recommendation['priority']) ? $recommendation['priority'] : 'normal';
                            $priorityClass = '';
                            $priorityLabel = '';
                            
                            switch($priority) {
                                case 'high':
                                    $priorityClass = 'bg-red-100 text-red-800 border-red-200';
                                    $priorityLabel = 'Alta';
                                    break;
                                case 'medium':
                                    $priorityClass = 'bg-yellow-100 text-yellow-800 border-yellow-200';
                                    $priorityLabel = 'Media';
                                    break;
                                default:
                                    $priorityClass = 'bg-blue-100 text-blue-800 border-blue-200';
                                    $priorityLabel = 'Normal';
                            }
                        ?>
                            <div class="py-4">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-lg font-semibold text-gray-800">Recomendación de seguridad</h3>
                                    <span class="px-2 py-1 text-xs rounded-full <?= $priorityClass ?> font-medium">Prioridad: <?= $priorityLabel ?></span>
                                </div>
                                <div class="text-gray-700 bg-gray-50 p-4 rounded border border-gray-200">
                                    <?= nl2br(htmlspecialchars($recommendation['recommendation'])) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="p-4 text-center text-blue-700 bg-blue-50 rounded border border-blue-200">
                        <i class="fas fa-info-circle mr-2"></i> No hay recomendaciones disponibles para este escaneo.
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="flex justify-end mt-6 mb-8">
            <a href="export_pdf.php?id=<?= $scan_id ?>" class="bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-lg flex items-center shadow-md transition duration-300" target="_blank">
                <i class="ri-file-pdf-line mr-2"></i>
                Exportar a PDF
            </a>
        </div>
    </div>
</body>
</html>
