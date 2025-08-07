const sqlite3 = require('sqlite3').verbose();
const path = require('path');
const fs = require('fs');

// Asegurar que el directorio data existe
const dataDir = path.join(__dirname, 'data');
if (!fs.existsSync(dataDir)) {
    fs.mkdirSync(dataDir);
}

// Ruta a la base de datos
const dbPath = path.join(dataDir, 'network_security.db');

// Crear conexión a la base de datos
const db = new sqlite3.Database(dbPath);

// Función para inicializar la base de datos
function setupDatabase() {
    console.log('Configurando base de datos SQLite...');
    
    db.serialize(() => {
        // Tabla de usuarios
        db.run(`
            CREATE TABLE IF NOT EXISTS users (
                user_id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                role TEXT DEFAULT 'user',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                last_login TIMESTAMP
            )
        `);

        // Tabla de intentos de inicio de sesión
        db.run(`
            CREATE TABLE IF NOT EXISTS login_attempts (
                attempt_id INTEGER PRIMARY KEY AUTOINCREMENT,
                email TEXT NOT NULL,
                ip_address TEXT,
                attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                success INTEGER DEFAULT 0
            )
        `);

        // Tabla de escaneos
        db.run(`
            CREATE TABLE IF NOT EXISTS scans (
                scan_id INTEGER PRIMARY KEY AUTOINCREMENT,
                scan_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                scan_duration REAL,
                total_devices INTEGER,
                total_vulnerabilities INTEGER
            )
        `);

        // Tabla de redes WiFi
        db.run(`
            CREATE TABLE IF NOT EXISTS wifi_networks (
                network_id INTEGER PRIMARY KEY AUTOINCREMENT,
                scan_id INTEGER,
                ssid TEXT,
                authentication TEXT,
                encryption TEXT,
                signal TEXT,
                security_key TEXT,
                FOREIGN KEY (scan_id) REFERENCES scans(scan_id) ON DELETE CASCADE
            )
        `);

        // Tabla de dispositivos
        db.run(`
            CREATE TABLE IF NOT EXISTS devices (
                device_id INTEGER PRIMARY KEY AUTOINCREMENT,
                scan_id INTEGER,
                ip_address TEXT,
                hostname TEXT,
                last_seen TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (scan_id) REFERENCES scans(scan_id) ON DELETE CASCADE
            )
        `);

        // Tabla de puertos abiertos
        db.run(`
            CREATE TABLE IF NOT EXISTS open_ports (
                port_id INTEGER PRIMARY KEY AUTOINCREMENT,
                device_id INTEGER,
                port_number INTEGER,
                service_name TEXT,
                banner TEXT,
                FOREIGN KEY (device_id) REFERENCES devices(device_id) ON DELETE CASCADE
            )
        `);

        // Tabla de vulnerabilidades
        db.run(`
            CREATE TABLE IF NOT EXISTS vulnerabilities (
                vuln_id INTEGER PRIMARY KEY AUTOINCREMENT,
                scan_id INTEGER,
                vuln_type TEXT,
                severity TEXT,
                description TEXT,
                affected_device TEXT,
                details TEXT,
                FOREIGN KEY (scan_id) REFERENCES scans(scan_id) ON DELETE CASCADE
            )
        `);

        // Tabla de recomendaciones de IA
        db.run(`
            CREATE TABLE IF NOT EXISTS ai_recommendations (
                rec_id INTEGER PRIMARY KEY AUTOINCREMENT,
                scan_id INTEGER,
                recommendation TEXT,
                FOREIGN KEY (scan_id) REFERENCES scans(scan_id) ON DELETE CASCADE
            )
        `);

        console.log('Base de datos configurada correctamente.');
    });
}

// Función para cerrar la conexión
function closeDatabase() {
    db.close((err) => {
        if (err) {
            console.error('Error al cerrar la base de datos:', err.message);
        } else {
            console.log('Conexión a la base de datos cerrada');
        }
    });
}

// Exportar funciones y objeto de base de datos
module.exports = {
    db,
    setupDatabase,
    closeDatabase
};

// Si se ejecuta este archivo directamente, configurar la base de datos
if (require.main === module) {
    setupDatabase();
    console.log('¡Base de datos creada! Presiona Ctrl+C para salir.');
}
