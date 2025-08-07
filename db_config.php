<?php
// Configuración de la base de datos SQLite
$db_path = __DIR__ . '/data/network_security.db';
$json_path = __DIR__ . '/data/json_storage';

// Asegurar que los directorios necesarios existen
if (!file_exists(__DIR__ . '/data')) {
    mkdir(__DIR__ . '/data', 0777, true);
}

if (!file_exists($json_path)) {
    mkdir($json_path, 0777, true);
}

// Clase para manejar almacenamiento alternativo usando JSON
class JSONDatabaseAdapter {
    private $basePath;
    
    public function __construct($basePath) {
        $this->basePath = $basePath;
    }
    
    // Simula una consulta SQL simple seleccionando registros de un archivo JSON
    public function query($sql, $params = []) {
        // Extraer el nombre de la tabla de la consulta SQL
        if (preg_match('/FROM\s+([\w_]+)/i', $sql, $matches)) {
            $table = $matches[1];
        } else {
            return [];
        }
        
        $data = $this->readTable($table);
        
        // Filtrar si hay una cláusula WHERE
        if (preg_match('/WHERE\s+([\w_]+)\s*=\s*\?/i', $sql, $matches)) {
            $column = $matches[1];
            $value = isset($params[0]) ? $params[0] : null;
            
            $result = [];
            foreach ($data as $row) {
                if (isset($row[$column]) && $row[$column] == $value) {
                    $result[] = $row;
                }
            }
            return $result;
        }
        
        return $data;
    }
    
    // Simula una consulta preparada
    public function prepare($sql) {
        return new JSONStatement($this, $sql);
    }
    
    // Ejecuta comandos SQL directamente
    public function exec($sql) {
        // Solo procesamos CREATE TABLE
        if (preg_match('/CREATE\s+TABLE\s+IF\s+NOT\s+EXISTS\s+([\w_]+)/i', $sql, $matches)) {
            $table = $matches[1];
            $this->createTableIfNotExists($table);
            return true;
        }
        return false;
    }
    
    // Leer datos de un archivo JSON
    public function readTable($table) {
        $filePath = $this->getTablePath($table);
        if (file_exists($filePath)) {
            $content = file_get_contents($filePath);
            return json_decode($content, true) ?: [];
        }
        return [];
    }
    
    // Guardar datos en un archivo JSON
    public function writeTable($table, $data) {
        $filePath = $this->getTablePath($table);
        file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
    }
    
    // Crear una tabla (archivo JSON) si no existe
    private function createTableIfNotExists($table) {
        $filePath = $this->getTablePath($table);
        if (!file_exists($filePath)) {
            $this->writeTable($table, []);
        }
    }
    
    // Insertar datos en una tabla
    public function insert($table, $data) {
        $tableData = $this->readTable($table);
        
        // Si la tabla tiene un campo autoincremental, lo manejamos
        if (isset($data['id']) && $data['id'] === null) {
            $maxId = 0;
            foreach ($tableData as $row) {
                if (isset($row['id']) && $row['id'] > $maxId) {
                    $maxId = $row['id'];
                }
            }
            $data['id'] = $maxId + 1;
        }
        
        // Añadir timestamps si no existen
        if (!isset($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        
        $tableData[] = $data;
        $this->writeTable($table, $tableData);
        return true;
    }
    
    // Obtener ruta de archivo para una tabla
    private function getTablePath($table) {
        return $this->basePath . '/' . $table . '.json';
    }
    
    // Método para establecer atributos (compatibilidad con PDO)
    public function setAttribute($attribute, $value) {
        // No hacemos nada, solo para compatibilidad
        return true;
    }
}

// Clase para manejar statements en el adaptador JSON
class JSONStatement {
    private $db;
    private $sql;
    private $params = [];
    private $result = [];
    
    public function __construct($db, $sql) {
        $this->db = $db;
        $this->sql = $sql;
    }
    
    public function execute($params = []) {
        $this->params = $params;
        
        // Manejar INSERT
        if (preg_match('/INSERT\s+INTO\s+([\w_]+)\s+\(([^\)]+)\)\s+VALUES\s+\(([^\)]+)\)/i', $this->sql, $matches)) {
            $table = $matches[1];
            $columns = array_map('trim', explode(',', $matches[2]));
            
            $data = [];
            foreach ($columns as $i => $column) {
                $data[$column] = isset($this->params[$i]) ? $this->params[$i] : null;
            }
            
            return $this->db->insert($table, $data);
        }
        
        // Manejar SELECT y otros
        $this->result = $this->db->query($this->sql, $this->params);
        return true;
    }
    
    public function fetch($fetchStyle = null) {
        if (empty($this->result)) {
            return false;
        }
        return array_shift($this->result);
    }
    
    public function fetchAll($fetchStyle = null) {
        return $this->result;
    }
}

// Función para conectar a la base de datos (SQLite o alternativa JSON)
function getDBConnection() {
    global $db_path, $json_path;
    
    try {
        // Intentar conexión con SQLite usando PDO si está disponible
        if (class_exists('PDO')) {
            try {
                // Crear o abrir la base de datos SQLite
                $dsn = 'sqlite:' . $db_path;
                $db = new PDO($dsn);
                
                // Configurar errores PDO
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // Habilitar claves foráneas
                $db->exec('PRAGMA foreign_keys = ON;');
                
                // Si todo va bien, devolver la conexión PDO
                return $db;
            } catch (PDOException $e) {
                // Si hay error con PDO, probar con nuestro adaptador JSON
                $message = "SQLite no disponible: " . $e->getMessage() . ". Usando almacenamiento alternativo basado en JSON.";
                error_log($message);
                
                // Devolver nuestro adaptador JSON que simula la interfaz PDO
                return new JSONDatabaseAdapter($json_path);
            }
        } else {
            // Si PDO no está disponible, usar siempre el adaptador JSON
            $message = "La extensión PDO no está disponible. Usando almacenamiento alternativo basado en JSON.";
            error_log($message);
            return new JSONDatabaseAdapter($json_path);
        }
    } catch (Exception $e) {
        // Capturar cualquier otro error durante la conexión
        $message = "Error inesperado al conectar a la base de datos: " . $e->getMessage() . ". Usando JSON como alternativa.";
        error_log($message);
        return new JSONDatabaseAdapter($json_path);
    }
}

// Función para obtener el último ID de escaneo, ahora en el ámbito global
function getLastScanId() {
    try {
        $db = getDBConnection();
        // Obtener el ID más reciente
        $stmt = $db->query("SELECT scan_id FROM scans ORDER BY scan_id DESC LIMIT 1");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            return $result['scan_id'];
        }
    } catch (PDOException $e) {
        error_log("Error al obtener el ID: " . $e->getMessage());
    }
    return null;
}
    global $db_path, $json_path;
    
    try {
        // Intentar conexión con SQLite usando PDO si está disponible
        if (class_exists('PDO')) {
            try {
                // Crear o abrir la base de datos SQLite
                $dsn = 'sqlite:' . $db_path;
                $db = new PDO($dsn);
                
                // Configurar errores PDO
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // Habilitar claves foráneas
                $db->exec('PRAGMA foreign_keys = ON;');
                
                // Si todo va bien, devolver la conexión PDO
                return $db;
            } catch (PDOException $e) {
                // Si hay error con PDO, probar con nuestro adaptador JSON
                $message = "SQLite no disponible: " . $e->getMessage() . ". Usando almacenamiento alternativo basado en JSON.";
                error_log($message);
                
                // Devolver nuestro adaptador JSON que simula la interfaz PDO
                return new JSONDatabaseAdapter($json_path);
            }
        } else if (extension_loaded('sqlite3')) {
            // SQLite3 está disponible pero no PDO, usamos JSON
            $message = "PDO no disponible pero SQLite3 sí. Usando almacenamiento alternativo basado en JSON.";
            error_log($message);
            return new JSONDatabaseAdapter($json_path);
        } else {
            // Ni PDO ni SQLite3 están disponibles, usamos JSON
            $message = "Ni PDO ni SQLite3 están disponibles. Usando almacenamiento alternativo basado en JSON.";
            error_log($message);
            return new JSONDatabaseAdapter($json_path);
        }
    } catch (Exception $e) {
        // Si hay cualquier otro error, intentamos con JSON
        try {
            $message = "Error general: " . $e->getMessage() . ". Intentando usar almacenamiento alternativo basado en JSON.";
            error_log($message);
            return new JSONDatabaseAdapter($json_path);
        } catch (Exception $jsonError) {
            // Si todo falla, mostramos mensaje y terminamos
            echo "<div style='background-color: #ffebee; color: #b71c1c; padding: 15px; margin: 20px; border-radius: 5px; border: 1px solid #ef9a9a;'>";        
            echo "<h3>Error de Conexión a la Base de Datos</h3>";        
            echo "<p><strong>" . $e->getMessage() . "</strong></p>";        
            echo "<p>Incluso el almacenamiento alternativo falló: " . $jsonError->getMessage() . "</p>";        
            echo "<h4>Posibles soluciones:</h4>";        
            echo "<ol>";        
            echo "<li>Asegúrese de que XAMPP está instalado correctamente.</li>";        
            echo "<li>Verifique los permisos de escritura en la carpeta 'data'.</li>";      
            echo "</ol>";        
            echo "</div>";        
            die();
        }
    }


// Función para inicializar la base de datos si no existe
function setupDatabase() {
    $db = getDBConnection();
    
    // Crear tabla de usuarios
    $db->exec('
        CREATE TABLE IF NOT EXISTS users (
            user_id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            role TEXT DEFAULT "user",
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            last_login TIMESTAMP
        )
    ');
    
    // Crear tabla de intentos de inicio de sesión
    $db->exec('
        CREATE TABLE IF NOT EXISTS login_attempts (
            attempt_id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT NOT NULL,
            ip_address TEXT,
            attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            success INTEGER DEFAULT 0
        )
    ');
    
    // Crear índices para mejorar el rendimiento
    $db->exec('CREATE INDEX IF NOT EXISTS idx_users_email ON users(email)');
    $db->exec('CREATE INDEX IF NOT EXISTS idx_login_attempts_email ON login_attempts(email)');
    
    echo "Base de datos SQLite configurada correctamente.";
}

// Si el archivo se ejecuta directamente, configurar la base de datos
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    setupDatabase();
}
?>
