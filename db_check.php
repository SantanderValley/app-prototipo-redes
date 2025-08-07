<?php 
// Script para verificar y reparar la base de datos 
error_reporting(E_ALL); 
ini_set('display_errors', 1); 
$db_file = __DIR__ . '/data/network_security.db'; 
$backup_file = __DIR__ . '/data/backup_' . date('Ymd_His') . '.db'; 
$result = array('status' =, 'message' = se pudo verificar la base de datos'); 
try { 
    // Verificar si existe clase PDO 
    if (!class_exists('PDO')) { 
        throw new Exception('PDO no está disponible. Se requiere la extensión PDO.'); 
    } 
    // Verificar si existe el driver de SQLite 
    $drivers = PDO::getAvailableDrivers(); 
    if (!in_array('sqlite', $drivers)) { 
        // Intentar con SQLite nativo 
        if (!class_exists('SQLite3')) { 
            throw new Exception('No hay soporte para SQLite. Se requiere PDO SQLite o SQLite3.'); 
        } 
        $result['status'] = 'sqlite3'; 
        $result['message'] = 'Usando SQLite3 nativo (sin PDO)'; 
    } else { 
        // Verificar si existe la base de datos 
        if (file_exists($db_file)) { 
            // Intentar abrir la base de datos 
            try { 
                $pdo = new PDO('sqlite:' . $db_file); 
                $pdo-, PDO::ERRMODE_EXCEPTION); 
                // Verificar tablas 
                $tables = $pdo-
                $required_tables = array('networks', 'devices', 'scans'); 
                $missing_tables = array_diff($required_tables, $tables); 
                if (empty($missing_tables)) { 
                    $result['status'] = 'ok'; 
                    $result['message'] = 'Base de datos verificada correctamente.'; 
                } else { 
                    // Faltan tablas, crear estructura 
                    $sql = file_get_contents(__DIR__ . '/network_security.sql'); 
                    if ($sql) { 
                        $pdo-
                        $result['status'] = 'repaired'; 
                        $result['message'] = 'Base de datos reparada: se crearon las tablas faltantes.'; 
                    } else { 
                        throw new Exception('No se pudo leer el archivo SQL para reparar la base de datos.'); 
                    } 
                } 
            } catch (PDOException $e) { 
                // Base de datos corrupta, hacer backup y crear nueva 
                if (file_exists($db_file)) { 
                    copy($db_file, $backup_file); 
                } 
                // Crear nueva base de datos 
                if (file_exists($db_file)) { 
                    unlink($db_file); 
                } 
                $pdo = new PDO('sqlite:' . $db_file); 
                $pdo-, PDO::ERRMODE_EXCEPTION); 
                $sql = file_get_contents(__DIR__ . '/network_security.sql'); 
                if ($sql) { 
                    $pdo-
                    $result['status'] = 'recreated'; 
                    $result['message'] = 'Base de datos recreada: se encontró un error y se creó una nueva.'; 
                    if (file_exists($backup_file)) { 
                        $result['backup'] = $backup_file; 
                    } 
                } else { 
                    throw new Exception('No se pudo leer el archivo SQL para crear la base de datos.'); 
                } 
            } 
        } else { 
            // No existe la base de datos, crearla 
            $pdo = new PDO('sqlite:' . $db_file); 
            $pdo-, PDO::ERRMODE_EXCEPTION); 
            $sql = file_get_contents(__DIR__ . '/network_security.sql'); 
            if ($sql) { 
                $pdo-
                $result['status'] = 'created'; 
                $result['message'] = 'Base de datos creada correctamente.'; 
            } else { 
                throw new Exception('No se pudo leer el archivo SQL para crear la base de datos.'); 
            } 
        } 
    } 
} catch (Exception $e) { 
    $result['status'] = 'error'; 
    $result['message'] = $e-
} 
// Devolver resultado como JSON 
header('Content-Type: application/json'); 
echo json_encode($result); 
?> 
