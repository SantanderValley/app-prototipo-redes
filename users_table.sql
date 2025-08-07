-- Tabla para almacenar los usuarios del sistema
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_login DATETIME,
    status TINYINT(1) DEFAULT 1 COMMENT '1=activo, 0=inactivo'
);

-- Índices para mejorar el rendimiento
CREATE INDEX idx_user_email ON users(email);
CREATE INDEX idx_user_status ON users(status);

-- Usuario administrador por defecto (contraseña: Admin123!)
-- La contraseña está hasheada usando password_hash con PASSWORD_DEFAULT
INSERT INTO users (name, email, password, role) VALUES 
('Administrador', 'admin@example.com', '$2y$10$Z4Uc3SmzLlqwxA3iibQbj.S0nSMYXTCUJH/vNq6q.YDV5rECRzzXi', 'admin');

-- Tablas de relación para mejorar el sistema en el futuro

-- Tabla para almacenar sesiones de usuario (para recordar sesiones entre visitas)
CREATE TABLE IF NOT EXISTS user_sessions (
    session_id VARCHAR(128) PRIMARY KEY,
    user_id INT NOT NULL,
    login_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    user_agent TEXT,
    is_active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Tabla para almacenar intentos de inicio de sesión (seguridad)
CREATE TABLE IF NOT EXISTS login_attempts (
    attempt_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    ip_address VARCHAR(45),
    attempted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    success TINYINT(1) DEFAULT 0
);
