<?php
// Incluir la configuración de la base de datos SQLite
require_once 'db_config.php';

// Iniciar sesión
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

try {
    // Conectar a la base de datos SQLite
    $db = getDBConnection();
    
    // Configuración de paginación
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $itemsPerPage = 10; // Número de elementos por página
    $offset = ($page - 1) * $itemsPerPage;
    
    // Obtener el total de escaneos
    $totalResult = $db->query("SELECT COUNT(*) as total FROM scans");
    $totalScans = $totalResult->fetchColumn();
    $totalPages = ceil($totalScans / $itemsPerPage);
    
    // Obtener los escaneos para la página actual
    $stmt = $db->prepare("SELECT * FROM scans ORDER BY scan_date DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    // AJAX Handler para paginación
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        $scans = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Preparar respuesta
        $response = [
            'scans' => $scans,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalScans' => $totalScans
        ];
        
        // Devolver resultados como JSON
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // Obtener conteo de dispositivos
    $devicesResult = $db->query("SELECT COUNT(*) AS total FROM devices");
    $devicesCount = $devicesResult->fetchColumn();
    
    // Obtener conteo de escaneos
    $scansResult = $db->query("SELECT COUNT(*) AS total FROM scans");
    $scansCount = $scansResult->fetchColumn();
    
    // Obtener conteo de vulnerabilidades
    $vulnsResult = $db->query("SELECT COUNT(*) AS total FROM vulnerabilities");
    $vulnsCount = $vulnsResult->fetchColumn();
    
    // Obtener los resultados para mostrar en la tabla
    $result = $stmt;
} catch (PDOException $e) {
    die("Error de base de datos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - WiFi Scanner</title>
    <!-- Tailwind CSS desde CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Iconos Remix -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }
        .active-tab {
            background-color: #3b82f6;
            color: white;
        }
        /* Estilos mejorados para sidebar */
        .sidebar {
            width: 250px;
            min-width: 250px;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 10;
        }
        /* Contenido principal */
        .main-content {
            flex: 1;
            min-width: 0; /* Importante para evitar que el contenido se desborde */
            overflow-x: auto;
        }
        /* Para dispositivos móviles */
        @media (max-width: 768px) {
            .page-container {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                min-width: 100%;
                height: auto;
                position: relative;
            }
        }
    </style>
</head>
<body class="bg-gray-100 flex">
    <!-- Sidebar -->
    <aside class="flex flex-col w-64 h-screen px-4 py-8 overflow-y-auto bg-white border-r rtl:border-r-0 rtl:border-l">
        <!-- Logo -->
        <div class="flex items-center space-x-2">
            <i class="ri-wifi-fill text-blue-600 text-2xl"></i>
            <span class="text-xl font-bold text-gray-800">EscanerRedes</span>
        </div>

        <div class="flex flex-col justify-between flex-1 mt-6">
            <!-- Navegación -->
            <nav>
                <a class="flex items-center px-4 py-2 text-gray-700 bg-gray-100 rounded-md" href="dashboard.php">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 11H5M19 11C20.1046 11 21 11.8954 21 13V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V13C3 11.8954 3.89543 11 5 11M19 11V9C19 7.89543 18.1046 7 17 7M5 11V9C5 7.89543 5.89543 7 7 7M7 7V5C7 3.89543 7.89543 3 9 3H15C16.1046 3 17 3.89543 17 5V7M7 7H17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span class="mx-4 font-medium">Dashboard</span>
                </a>

                <!-- <a class="flex items-center px-4 py-2 mt-5 text-gray-600 transition-colors duration-300 transform rounded-md hover:bg-gray-100 hover:text-gray-700" href="#">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15 12C15 13.6569 13.6569 15 12 15C10.3431 15 9 13.6569 9 12C9 10.3431 10.3431 9 12 9C13.6569 9 15 10.3431 15 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M19 11H5M19 11C20.1046 11 21 11.8954 21 13V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V13C3 11.8954 3.89543 11 5 11M19 11V9C19 7.89543 18.1046 7 17 7M5 11V9C5 7.89543 5.89543 7 7 7M7 7V5C7 3.89543 7.89543 3 9 3H15C16.1046 3 17 3.89543 17 5V7M7 7H17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span class="mx-4 font-medium">Configuración</span>
                </a> -->
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
    <div class="flex-1 flex flex-col p-6">
        <!-- Header con tabs -->
        <div class="mb-8 bg-white border rounded-lg p-2">
            <nav class="flex space-x-4" aria-label="Tabs">
                <a href="#" data-tab="scan" class="tab-link bg-blue-600 text-white rounded-md px-4 py-2 text-sm font-medium">
                    Escaneo de red
                </a>
                <a href="#" data-tab="history" class="tab-link text-gray-600 hover:text-gray-800 rounded-md px-4 py-2 text-sm font-medium">
                    Historial de escaneos
                </a>
            </nav>
        </div>
        
        <!-- Contenido central -->
        <main class="flex-1">
            <!-- Tab: Escaneo de red -->
            <div id="tab-scan" class="tab-content bg-white p-8 rounded-lg shadow h-full flex flex-col items-center justify-center">
                <h1 class="text-3xl font-bold text-gray-800 mb-4">Iniciar escaneo de red</h1>
                <p class="text-gray-600 text-center max-w-md mb-8">
                    Detecta dispositivos conectados, identifica servicios y encuentra vulnerabilidades en segundos. Presiona el botón para comenzar un nuevo escaneo de tu red local.
                </p>
                <button id="start-scan" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center space-x-2 shadow-lg">
                    <i class="fas fa-search"></i>
                    <span>Iniciar Escaneo</span>
                </button>
            </div>

            <!-- Tab: Historial de escaneos -->
            <div id="tab-history" class="tab-content hidden">
                <!-- Métricas -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-lg shadow-sm text-center border">
                        <h3 class="text-4xl font-bold text-gray-800 mb-2" id="devices-count"><?php echo $devicesCount; ?></h3>
                        <p class="text-gray-600">Dispositivos Detectados</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-sm text-center border">
                        <h3 class="text-4xl font-bold text-gray-800 mb-2" id="scans-count"><?php echo $scansCount; ?></h3>
                        <p class="text-gray-600">Escaneos Realizados</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-sm text-center border">
                        <h3 class="text-4xl font-bold text-gray-800 mb-2" id="vulnerabilities-count"><?php echo $vulnsCount; ?></h3>
                        <p class="text-gray-600">Vulnerabilidades Detectadas</p>
                    </div>
                </div>

                <!-- Tabla de historial -->
                <div id="scan-history-table" class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-4 border-b flex justify-between items-center">
                        <h3 class="text-xl font-semibold text-gray-700">Historial de Escaneos</h3>
                        <div class="text-sm text-gray-500">
                            <?php 
                            $scanCount = $stmt->rowCount();
                            echo "Mostrando $scanCount de $totalScans escaneos";
                            ?>
                        </div>
                    </div>
                    
                    <?php if ($scanCount > 0): ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duración</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dispositivos</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vulnerabilidades</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">#<?php echo $row['scan_id']; ?></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500"><?php echo date('d/m/Y H:i', strtotime($row['scan_date'])); ?></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900"><?php echo round($row['scan_duration'], 2); ?> seg</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900"><?php echo $row['total_devices']; ?></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm <?php echo $row['total_vulnerabilities'] > 0 ? 'text-red-600' : 'text-green-600'; ?>">
                                                    <?php echo $row['total_vulnerabilities']; ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="scan_details.php?id=<?php echo $row['scan_id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">Ver</a>
                                                <a href="export_pdf.php?id=<?php echo $row['scan_id']; ?>" class="text-green-600 hover:text-green-900 mr-3" target="_blank">PDF</a>
                                                <a href="#" data-scan-id="<?php echo $row['scan_id']; ?>" class="delete-scan text-red-600 hover:text-red-900">Eliminar</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Paginador -->
                        <div class="flex items-center justify-between p-4 border-t">
                            <a href="#" data-page="<?php echo max(1, $page - 1); ?>" class="paginator-btn flex items-center px-5 py-2 text-sm text-gray-700 capitalize transition-colors duration-200 bg-white border rounded-md gap-x-2 hover:bg-gray-100 <?php echo $page <= 1 ? 'pointer-events-none opacity-50' : ''; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 rtl:-scale-x-100">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 15.75L3 12m0 0l3.75-3.75M3 12h18"></path>
                                </svg>
                                <span>Anterior</span>
                            </a>

                            <div class="items-center hidden lg:flex gap-x-3">
                                <?php for($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                    <a href="#" data-page="<?php echo $i; ?>" class="paginator-btn px-2 py-1 text-sm rounded-md <?php echo $i == $page ? 'text-blue-500 bg-blue-100' : 'text-gray-500 hover:bg-gray-100'; ?>"><?php echo $i; ?></a>
                                <?php endfor; ?>
                            </div>

                            <a href="#" data-page="<?php echo min($totalPages, $page + 1); ?>" class="paginator-btn flex items-center px-5 py-2 text-sm text-gray-700 capitalize transition-colors duration-200 bg-white border rounded-md gap-x-2 hover:bg-gray-100 <?php echo $page >= $totalPages ? 'pointer-events-none opacity-50' : ''; ?>">
                                <span>Siguiente</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 rtl:-scale-x-100">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3"></path>
                                </svg>
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="p-6 text-center text-gray-500">
                            No hay escaneos disponibles. Realice un nuevo escaneo para ver resultados.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Configuración de las tabs
            const tabLinks = document.querySelectorAll('.tab-link');
            const tabContents = document.querySelectorAll('.tab-content');
            let currentPage = <?php echo $page; ?>;

            // Función para mostrar una tab específica
            function showTab(tabId) {
                // Ocultar todos los contenidos de tabs
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                });

                // Desactivar todas las pestañas
                tabLinks.forEach(link => {
                    link.classList.remove('bg-blue-600', 'text-white');
                    link.classList.add('text-gray-600', 'hover:text-gray-800');
                });

                // Mostrar el contenido de la tab seleccionada
                const selectedContent = document.getElementById(`tab-${tabId}`);
                if (selectedContent) {
                    selectedContent.classList.remove('hidden');
                }

                // Activar la pestaña seleccionada
                const selectedTab = document.querySelector(`[data-tab="${tabId}"]`);
                if (selectedTab) {
                    selectedTab.classList.remove('text-gray-600', 'hover:text-gray-800');
                    selectedTab.classList.add('bg-blue-600', 'text-white');
                }

                // Guardar en localStorage
                localStorage.setItem('activeTab', tabId);
                
                // Si la tab es 'history', cargar la primera página de escaneos
                if (tabId === 'history') {
                    loadScans(1);
                }
            }

            // Event listeners para las tabs
            tabLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const tabId = this.getAttribute('data-tab');
                    showTab(tabId);
                });
            });

            // Inicializar la tab activa (desde localStorage o por defecto)
            const activeTab = localStorage.getItem('activeTab') || 'scan';
            showTab(activeTab);

            // Inicializar el botón de inicio de escaneo
            const startScanButton = document.getElementById('start-scan');
            if (startScanButton) {
                startScanButton.addEventListener('click', function() {
                    // Cambiar el texto del botón para indicar que está procesando
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Escaneando...</span>';
                    this.disabled = true;
                    
                    // Mostrar indicador de carga
                    const scanStatusDiv = document.createElement('div');
                    scanStatusDiv.className = 'fixed bottom-4 right-4 bg-gray-800 text-white p-4 rounded-lg shadow-lg flex items-center space-x-3';
                    scanStatusDiv.innerHTML = `
                        <i class="fas fa-spinner fa-spin"></i>
                        <span>Escaneo en progreso... No cierres esta página.</span>
                    `;
                    document.body.appendChild(scanStatusDiv);

                    let pollingInterval;

                    const pollScanStatus = () => {
                        fetch('check_scan_status.php')
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'completed') {
                                    clearInterval(pollingInterval);
                                    if (document.body.contains(scanStatusDiv)) {
                                        document.body.removeChild(scanStatusDiv);
                                    }
                                    this.disabled = false;
                                    this.innerHTML = originalText;
                                    if (data.scan_id) {
                                        window.location.href = `scan_details.php?id=${data.scan_id}`;
                                    } else {
                                        alert('Escaneo completado, pero no se pudo obtener el ID del escaneo.');
                                    }
                                } else if (data.status === 'error') {
                                    clearInterval(pollingInterval);
                                    if (document.body.contains(scanStatusDiv)) {
                                        document.body.removeChild(scanStatusDiv);
                                    }
                                    this.disabled = false;
                                    this.innerHTML = originalText;
                                    let errorMsg = 'Ocurrió un error durante el escaneo.';
                                    if(data.message) errorMsg += '\n' + data.message;
                                    if(data.log) errorMsg += '\n\nLog: ' + data.log;
                                    alert(errorMsg);
                                }
                                // Si el estado es 'processing', no hacemos nada y esperamos la siguiente llamada.
                            })
                            .catch(error => {
                                clearInterval(pollingInterval);
                                if (document.body.contains(scanStatusDiv)) {
                                    document.body.removeChild(scanStatusDiv);
                                }
                                console.error('Error al verificar el estado del escaneo:', error);
                                alert('Error de comunicación al verificar el estado del escaneo.');
                                this.disabled = false;
                                this.innerHTML = originalText;
                            });
                    };

                    // Llamada AJAX para ejecutar el script
                    fetch('run_scanner.php', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.status === 'processing') {
                            // El indicador de carga ya está visible. Empezamos a sondear.
                            pollingInterval = setInterval(pollScanStatus, 5000);
                        } else {
                            // Si el inicio del escaneo falla, limpiamos la UI.
                            if (document.body.contains(scanStatusDiv)) {
                                document.body.removeChild(scanStatusDiv);
                            }
                            alert('Error al iniciar el escaneo: ' + (data.message || 'Respuesta inesperada del servidor.'));
                            this.disabled = false;
                            this.innerHTML = originalText;
                        }
                    })
                    .catch(error => {
                        if (document.body.contains(scanStatusDiv)) {
                            document.body.removeChild(scanStatusDiv);
                        }
                        console.error('Error en la solicitud de escaneo inicial:', error);
                        alert('Error al ejecutar el escaneo: ' + error.message);
                        this.disabled = false;
                        this.innerHTML = originalText;
                    });
                });
            }
            
            // Función para cargar escaneos mediante AJAX
            function loadScans(page) {
                currentPage = page;
                fetch(`dashboard.php?page=${page}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    const table = document.getElementById('scan-history-table');
                    let html = `
                    <div class="p-4 border-b flex justify-between items-center">
                        <h3 class="text-xl font-semibold text-gray-700">Historial de Escaneos</h3>
                        <div class="text-sm text-gray-500">
                            Mostrando ${data.scans.length} de ${data.totalScans} escaneos
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duración</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dispositivos</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vulnerabilidades</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">`;

                    data.scans.forEach(scan => {
                        const date = new Date(scan.scan_date).toLocaleString('es-ES');
                        const vulnerabilityClass = scan.total_vulnerabilities > 0 ? 'text-red-600' : 'text-green-600';
                        html += `
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">#${scan.scan_id}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">${date}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${Math.round(scan.scan_duration * 100) / 100} seg</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${scan.total_devices}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm ${vulnerabilityClass}">
                                        ${scan.total_vulnerabilities}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="scan_details.php?id=${scan.scan_id}" class="text-blue-600 hover:text-blue-900 mr-3">Ver</a>
                                    <a href="export_pdf.php?id=${scan.scan_id}" class="text-green-600 hover:text-green-900 mr-3" target="_blank">PDF</a>
                                    <a href="#" data-scan-id="${scan.scan_id}" class="delete-scan text-red-600 hover:text-red-900">Eliminar</a>
                                </td>
                            </tr>`;
                    });

                    html += `
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Paginador -->
                    <div class="flex items-center justify-between p-4 border-t">
                        <a href="#" data-page="${Math.max(1, data.currentPage - 1)}" class="paginator-btn flex items-center px-5 py-2 text-sm text-gray-700 capitalize transition-colors duration-200 bg-white border rounded-md gap-x-2 hover:bg-gray-100 ${data.currentPage <= 1 ? 'pointer-events-none opacity-50' : ''}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 rtl:-scale-x-100">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 15.75L3 12m0 0l3.75-3.75M3 12h18"></path>
                            </svg>
                            <span>Anterior</span>
                        </a>

                        <div class="items-center hidden lg:flex gap-x-3">`;
                        
                    for(let i = Math.max(1, data.currentPage - 2); i <= Math.min(data.totalPages, data.currentPage + 2); i++) {
                        html += `<a href="#" data-page="${i}" class="paginator-btn px-2 py-1 text-sm rounded-md ${i == data.currentPage ? 'text-blue-500 bg-blue-100' : 'text-gray-500 hover:bg-gray-100'}">${i}</a>`;
                    }
                        
                    html += `</div>

                        <a href="#" data-page="${Math.min(data.totalPages, data.currentPage + 1)}" class="paginator-btn flex items-center px-5 py-2 text-sm text-gray-700 capitalize transition-colors duration-200 bg-white border rounded-md gap-x-2 hover:bg-gray-100 ${data.currentPage >= data.totalPages ? 'pointer-events-none opacity-50' : ''}">
                            <span>Siguiente</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 rtl:-scale-x-100">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3"></path>
                            </svg>
                        </a>
                    </div>`;

                    table.innerHTML = html;

                    // Agregar eventos a los botones de paginación
                    document.querySelectorAll('.paginator-btn').forEach(btn => {
                        btn.addEventListener('click', (e) => {
                            e.preventDefault();
                            if (!btn.classList.contains('pointer-events-none')) {
                                loadScans(parseInt(btn.dataset.page));
                            }
                        });
                    });
                    
                    // Inicializar los botones de eliminar en la tabla actualizada
                    setupDeleteButtons();
                })
                .catch(error => {
                    console.error('Error al cargar los escaneos:', error);
                });
            }
            
            // Inicializar el paginador si estamos en la tab de historial
            document.querySelectorAll('.paginator-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (!btn.classList.contains('pointer-events-none')) {
                        loadScans(parseInt(btn.dataset.page));
                    }
                });
            });
            
            // Inicializar los botones de eliminar
            setupDeleteButtons();
            
            // Función para configurar los botones de eliminar
            function setupDeleteButtons() {
                document.querySelectorAll('.delete-scan').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        const scanId = btn.dataset.scanId;
                        
                        if (confirm('¿Está seguro de que desea eliminar este escaneo? Esta acción no se puede deshacer.')) {
                            // Crear un formulario para enviar la solicitud POST
                            const form = new FormData();
                            form.append('scan_id', scanId);
                            
                            // Mostrar indicador de carga
                            const row = btn.closest('tr');
                            row.style.opacity = '0.5';
                            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                            
                            // Enviar solicitud para eliminar
                            fetch('delete_scan.php', {
                                method: 'POST',
                                body: form
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Mostrar mensaje de éxito
                                    const successDiv = document.createElement('div');
                                    successDiv.className = 'fixed top-0 left-0 right-0 bg-green-600 text-white py-2 px-4 text-center z-50';
                                    successDiv.innerHTML = data.message;
                                    document.body.appendChild(successDiv);
                                    
                                    // Eliminar el mensaje después de 3 segundos
                                    setTimeout(() => {
                                        document.body.removeChild(successDiv);
                                    }, 3000);
                                    
                                    // Recargar la tabla
                                    loadScans(currentPage);
                                } else {
                                    // Mostrar mensaje de error
                                    alert(data.message || 'Error al eliminar el escaneo');
                                    row.style.opacity = '1';
                                    btn.innerHTML = 'Eliminar';
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('Error al procesar la solicitud');
                                row.style.opacity = '1';
                                btn.innerHTML = 'Eliminar';
                            });
                        }
                    });
                });
            }
        });
    </script>
</body>
</html>
