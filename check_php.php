<?php 
// Script para verificar la configuración de PHP 
echo "^<h1^>Diagnóstico de PHP^</h1^>"; 
echo "^<p^>Versión de PHP: " . phpversion() . "^</p^>"; 
echo "^<p^>Extension Directory: " . ini_get('extension_dir') . "^</p^>"; 
echo "^<h2^>Extensiones cargadas:^</h2^>"; 
echo "^<ul^>"; 
$loaded = get_loaded_extensions(); 
foreach($loaded as $ext) { 
    echo "^<li^>$ext^</li^>"; 
} 
echo "^</ul^>"; 
echo "^<h2^>Estado de PDO:^</h2^>"; 
if(class_exists('PDO')) { 
    echo "^<p style='color:green'^>PDO está disponible^</p^>"; 
    echo "^<h3^>Drivers PDO disponibles:^</h3^>"; 
    echo "^<ul^>"; 
    $drivers = PDO::getAvailableDrivers(); 
    foreach($drivers as $driver) { 
        echo "^<li^>$driver^</li^>"; 
    } 
    echo "^</ul^>"; 
} else { 
    echo "^<p style='color:red'^>PDO no está disponible^</p^>"; 
} 
if(class_exists('SQLite3')) { 
    echo "^<p style='color:green'^>SQLite3 está disponible^</p^>"; 
} else { 
    echo "^<p style='color:red'^>SQLite3 no está disponible^</p^>"; 
} 
// Verificar estado de la base de datos 
echo "^<h2^>Estado de la Base de Datos:^</h2^>"; 
$db_file = __DIR__ . '/data/network_security.db'; 
if (file_exists($db_file)) { 
    echo "^<p style='color:green'^>Archivo de base de datos encontrado: " . realpath($db_file) . "^</p^>"; 
    try { 
        if (class_exists('PDO')) { 
            $pdo = new PDO('sqlite:' . $db_file); 
            $pdo-, PDO::ERRMODE_EXCEPTION); 
            $tables = $pdo-
            echo "^<p^>Tablas encontradas: " . implode(', ', $tables) . "^</p^>"; 
        } else if (class_exists('SQLite3')) { 
            $db = new SQLite3($db_file); 
            $result = $db-
            $tables = array(); 
            while ($row = $result- { 
                $tables[] = $row['name']; 
            } 
            echo "^<p^>Tablas encontradas: " . implode(', ', $tables) . "^</p^>"; 
        } 
    } catch (Exception $e) { 
        echo "^<p style='color:red'^>Error al conectar con la base de datos: " . $e- . "^</p^>"; 
    } 
} else { 
    echo "^<p style='color:red'^>Archivo de base de datos no encontrado: " . $db_file . "^</p^>"; 
} 
?> 
