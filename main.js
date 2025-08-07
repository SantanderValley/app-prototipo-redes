const { app, BrowserWindow, ipcMain } = require('electron');
const path = require('path');
const express = require('express');
const bodyParser = require('body-parser');
const { execFile } = require('child_process');
const { db, setupDatabase } = require('./database_setup');

// Configuración del servidor Express
const server = express();
const PORT = 3000;

// Configurar middleware
server.use(bodyParser.json());
server.use(bodyParser.urlencoded({ extended: true }));
server.use(express.static(path.join(__dirname, 'public')));

// Variable para almacenar la ventana principal
let mainWindow;

// Función para crear la ventana principal
function createWindow() {
    // Crear la ventana del navegador
    mainWindow = new BrowserWindow({
        width: 1200,
        height: 800,
        webPreferences: {
            nodeIntegration: true,
            contextIsolation: false,
            preload: path.join(__dirname, 'preload.js')
        },
        icon: path.join(__dirname, 'public/icons/app-icon.png')
    });

    // Cargar la URL del servidor local
    mainWindow.loadURL(`http://localhost:${PORT}`);

    // Abrir DevTools en desarrollo
    // mainWindow.webContents.openDevTools();

    // Evento cuando la ventana está cerrada
    mainWindow.on('closed', function () {
        mainWindow = null;
    });
}

// Inicializar la aplicación
app.on('ready', () => {
    // Configurar la base de datos
    setupDatabase();

    // Iniciar el servidor Express
    server.listen(PORT, () => {
        console.log(`Servidor corriendo en http://localhost:${PORT}`);
        createWindow();
    });
});

// Salir cuando todas las ventanas estén cerradas
app.on('window-all-closed', () => {
    if (process.platform !== 'darwin') {
        app.quit();
    }
});

app.on('activate', () => {
    if (mainWindow === null) {
        createWindow();
    }
});

// Manejar el cierre de la aplicación
app.on('will-quit', () => {
    // Cerrar la base de datos
    db.close();
});

// Rutas de la API para la aplicación
// Aquí convertiremos las funcionalidades de PHP a Express

// Ruta de ejemplo para login
server.post('/api/login', (req, res) => {
    const { email, password } = req.body;
    
    // Consulta a la base de datos SQLite
    db.get('SELECT user_id, name, password FROM users WHERE email = ?', [email], (err, user) => {
        if (err) {
            return res.status(500).json({ error: 'Error de base de datos' });
        }
        
        if (!user) {
            return res.status(401).json({ error: 'Usuario no encontrado' });
        }
        
        // Aquí necesitaríamos verificar la contraseña con bcrypt
        // Por ahora, implementaremos una verificación simple
        
        // Actualizar último login
        db.run('UPDATE users SET last_login = datetime("now") WHERE user_id = ?', [user.user_id]);
        
        // Devolver información del usuario (sin la contraseña)
        return res.json({
            user_id: user.user_id,
            name: user.name
        });
    });
});

// Función para ejecutar el script Python
function runPythonScanner() {
    // Ruta al ejecutable Python empaquetado
    const pythonExecutablePath = path.join(__dirname, 'bin', 'wifi_scanner.exe');
    
    return new Promise((resolve, reject) => {
        execFile(pythonExecutablePath, (error, stdout, stderr) => {
            if (error) {
                console.error(`Error al ejecutar el scanner: ${error}`);
                return reject(error);
            }
            console.log(`Salida del scanner: ${stdout}`);
            if (stderr) console.error(`Error del scanner: ${stderr}`);
            resolve(stdout);
        });
    });
}

// API para iniciar un escaneo
server.post('/api/scan', (req, res) => {
    runPythonScanner()
        .then(output => {
            res.json({ success: true, message: 'Escaneo completado', output });
        })
        .catch(error => {
            res.status(500).json({ success: false, message: 'Error al ejecutar el escaneo', error: error.message });
        });
});

// IPC handlers para comunicación entre el proceso principal y el renderer
ipcMain.on('run-scan', (event) => {
    runPythonScanner()
        .then(output => {
            event.reply('scan-completed', { success: true, output });
        })
        .catch(error => {
            event.reply('scan-completed', { success: false, error: error.message });
        });
});
