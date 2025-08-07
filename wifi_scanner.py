import socket
import threading
import queue
import time
import ssl
import subprocess
import re
import os
import mysql.connector
from datetime import datetime
from openai import OpenAI
from reportlab.lib import colors
from reportlab.lib.pagesizes import letter
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle
from reportlab.lib.styles import getSampleStyleSheet

# API key de OpenAI
API_KEY = "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"

# Configuración de la base de datos MySQL
DB_CONFIG = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'network_security'
}

class NetworkSecuritySuite:
    def __init__(self):
        self.vulnerabilities = []
        self.devices = []
        self.network_info = {}
        self.ai_client = OpenAI(api_key=API_KEY)
        self.scan_id = None
        self.scan_start_time = None
        self.common_ports = {
            21: "FTP",
            22: "SSH",
            23: "Telnet",
            25: "SMTP",
            53: "DNS",
            80: "HTTP",
            443: "HTTPS",
            445: "SMB",
            3306: "MySQL",
            3389: "RDP",
            8080: "HTTP-Proxy"
        }
        
        # Inicializar la base de datos
        self.setup_database()

    def get_local_network(self):
        """Obtiene la dirección IP local y el prefijo de red"""
        s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
        try:
            s.connect(('8.8.8.8', 1))
            local_ip = s.getsockname()[0]
            network_prefix = '.'.join(local_ip.split('.')[:-1])
            return local_ip, network_prefix
        except:
            return '127.0.0.1', '127.0.0'
        finally:
            s.close()

    def scan_port(self, ip, port, q):
        """Escanea un puerto específico"""
        try:
            sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
            sock.settimeout(1)
            result = sock.connect_ex((ip, port))
            if result == 0:
                service = self.common_ports.get(port, "Desconocido")
                banner = self.get_service_banner(sock)
                q.put((ip, port, service, banner))
            sock.close()
        except:
            pass

    def get_service_banner(self, sock):
        """Obtiene el banner del servicio"""
        try:
            sock.send(b'\r\n')
            return sock.recv(1024).decode('utf-8', errors='ignore')
        except:
            return None

    def scan_host(self, ip, q):
        """Escanea todos los puertos comunes en un host"""
        threads = []
        port_queue = queue.Queue()

        for port in self.common_ports:
            t = threading.Thread(target=self.scan_port, args=(ip, port, port_queue))
            t.daemon = True
            threads.append(t)
            t.start()

        for t in threads:
            t.join()

        ports_info = []
        while not port_queue.empty():
            ports_info.append(port_queue.get())

        if ports_info:
            q.put((ip, ports_info))

    def get_wifi_info(self):
        """Obtiene información de la red WiFi actual"""
        try:
            output = subprocess.check_output(
                ['netsh', 'wlan', 'show', 'interfaces'],
                encoding='utf-8',
                errors='ignore'
            )

            wifi_info = {}
            for line in output.split('\n'):
                if 'SSID' in line and 'BSSID' not in line:
                    wifi_info['ssid'] = line.split(':')[1].strip()
                elif 'Autenticación' in line:
                    wifi_info['authentication'] = line.split(':')[1].strip()
                elif 'Cifrado' in line:
                    wifi_info['encryption'] = line.split(':')[1].strip()
                elif 'Señal' in line:
                    wifi_info['signal'] = line.split(':')[1].strip()

            if wifi_info.get('ssid'):
                # Obtener detalles adicionales del perfil
                output = subprocess.check_output(
                    ['netsh', 'wlan', 'show', 'profile', 'name=' + wifi_info['ssid'], 'key=clear'],
                    encoding='utf-8',
                    errors='ignore'
                )
                wifi_info['details'] = self._parse_profile_details(output)

            return wifi_info
        except Exception as e:
            print(f"Error al obtener información WiFi: {e}")
            return {}

    def _parse_profile_details(self, profile_output):
        """Analiza los detalles del perfil de red"""
        details = {}
        patterns = {
            'key_type': r'Tipo de clave\s*:\s*(.+)',
            'key_length': r'Longitud de la clave\s*:\s*(.+)',
            'security_key': r'Contenido de la clave\s*:\s*(.+)',
            'pmf_capable': r'Capable PMF\s*:\s*(.+)',
            'pmf_enabled': r'PMF habilitado\s*:\s*(.+)'
        }

        for key, pattern in patterns.items():
            match = re.search(pattern, profile_output)
            if match:
                details[key] = match.group(1).strip()

        return details

    def check_vulnerabilities(self):
        """Analiza vulnerabilidades en la red"""
        # Verificar configuración WiFi
        if self.network_info:
            auth = self.network_info.get('authentication', '').lower()
            encryption = self.network_info.get('encryption', '').lower()
            details = self.network_info.get('details', {})

            # Verificar autenticación
            if 'wpa3' not in auth:
                self.vulnerabilities.append({
                    'type': 'authentication',
                    'severity': 'high' if 'wpa2' not in auth else 'medium',
                    'description': 'Método de autenticación no óptimo',
                    'current': auth
                })

            # Verificar cifrado
            if 'ccmp' not in encryption:
                self.vulnerabilities.append({
                    'type': 'encryption',
                    'severity': 'high',
                    'description': 'Método de cifrado débil',
                    'current': encryption
                })

            # Verificar contraseña
            if 'security_key' in details:
                password = details['security_key']
                if len(password) < 12:
                    self.vulnerabilities.append({
                        'type': 'password',
                        'severity': 'high',
                        'description': 'Contraseña débil',
                        'details': 'Longitud menor a 12 caracteres'
                    })

        # Verificar puertos abiertos peligrosos
        for device in self.devices:
            ip, ports_info = device
            for port_info in ports_info:
                port = port_info[1]
                service = port_info[2]
                
                if port in [21, 23, 445]:  # Puertos potencialmente peligrosos
                    self.vulnerabilities.append({
                        'type': 'open_port',
                        'severity': 'high',
                        'description': f'Puerto potencialmente peligroso abierto: {port} ({service})',
                        'device': ip
                    })

    def get_ai_recommendations(self):
        """Obtiene recomendaciones de seguridad usando IA"""
        if not self.ai_client:
            return "IA no disponible - API key no configurada"

        try:
            # Crear un prompt detallado
            prompt = f"""Analiza la siguiente información de seguridad de red y proporciona recomendaciones 
            detalladas y prácticas para mejorar la seguridad:

            Red WiFi:
            {self.network_info}

            Dispositivos detectados:
            {self.devices}

            Vulnerabilidades encontradas:
            {self.vulnerabilities}

            Por favor, proporciona:
            1. Evaluación general del nivel de seguridad
            2. Recomendaciones específicas para cada vulnerabilidad
            3. Pasos prácticos para implementar las mejoras
            4. Mejores prácticas adicionales de seguridad
            5. Posibles consecuencias si no se implementan las mejoras
            """

            response = self.ai_client.chat.completions.create(
                model="gpt-3.5-turbo",
                messages=[
                    {"role": "system", "content": "Eres un experto en ciberseguridad especializado en seguridad de redes."},
                    {"role": "user", "content": prompt}
                ],
                temperature=0.7,
                max_tokens=1500
            )

            return response.choices[0].message.content
        except Exception as e:
            return f"Error al obtener recomendaciones de IA: {str(e)}"

    def scan_network(self):
        """Escanea la red completa"""
        print("=== Iniciando Análisis de Seguridad de Red ===\n")
        
        # Guardar tiempo de inicio
        self.scan_start_time = time.time()
        
        # 1. Obtener información de la red WiFi
        print("Obteniendo información de la red WiFi...")
        self.network_info = self.get_wifi_info()
        
        # 2. Escanear dispositivos y puertos
        print("Escaneando dispositivos en la red...")
        local_ip, network_prefix = self.get_local_network()
        
        q = queue.Queue()
        threads = []

        for i in range(1, 255):
            ip = f"{network_prefix}.{i}"
            thread = threading.Thread(target=self.scan_host, args=(ip, q))
            thread.daemon = True
            threads.append(thread)
            thread.start()

            if len(threads) >= 50:  # Limitar hilos concurrentes
                for t in threads:
                    t.join()
                threads = []

        for thread in threads:
            thread.join()

        while not q.empty():
            self.devices.append(q.get())

        # 3. Analizar vulnerabilidades
        print("Analizando vulnerabilidades...")
        self.check_vulnerabilities()

        # 4. Obtener recomendaciones de IA
        print("Generando recomendaciones con IA...")
        ai_recommendations = self.get_ai_recommendations()

        # 5. Generar reporte
        self.generate_report(ai_recommendations, time.time() - self.scan_start_time)

    def generate_pdf_report(self, ai_recommendations, scan_time):
        """Genera un reporte en PDF"""
        os.system('cls' if os.name == 'nt' else 'clear')
        
        pdf_filename = f"security_report_{datetime.now().strftime('%Y%m%d_%H%M%S')}.pdf"
        pdf = SimpleDocTemplate(pdf_filename, pagesize=letter)
        elements = []
        styles = getSampleStyleSheet()

        # Título del reporte
        title = Paragraph("=== Reporte de Seguridad de Red ===", styles['Title'])
        elements.append(title)
        elements.append(Spacer(1, 12))

        # Información de la red WiFi
        if self.network_info:
            wifi_info = [
                ["SSID", self.network_info.get('ssid', 'No disponible')],
                ["Autenticación", self.network_info.get('authentication', 'No disponible')],
                ["Cifrado", self.network_info.get('encryption', 'No disponible')],
                ["Señal", self.network_info.get('signal', 'No disponible')]
            ]
            wifi_table = Table(wifi_info)
            wifi_table.setStyle(TableStyle([
                ('BACKGROUND', (0, 0), (-1, 0), colors.grey),
                ('TEXTCOLOR', (0, 0), (-1, 0), colors.whitesmoke),
                ('ALIGN', (0, 0), (-1, -1), 'CENTER'),
                ('FONTNAME', (0, 0), (-1, 0), 'Helvetica-Bold'),
                ('BOTTOMPADDING', (0, 0), (-1, 0), 12),
                ('BACKGROUND', (0, 1), (-1, -1), colors.beige),
                ('GRID', (0, 0), (-1, -1), 1, colors.black)
            ]))
            elements.append(wifi_table)
            elements.append(Spacer(1, 12))

        # Dispositivos detectados
        if self.devices:
            device_info = []
            for device in sorted(self.devices, key=lambda x: [int(i) for i in x[0].split('.')]):
                ip, ports_info = device
                ports = ', '.join([f"{port_info[1]}/TCP ({port_info[2]})" for port_info in ports_info])
                device_info.append([ip, ports])
            device_table = Table([["Dispositivo", "Puertos Abiertos"]] + device_info)
            device_table.setStyle(TableStyle([
                ('BACKGROUND', (0, 0), (-1, 0), colors.grey),
                ('TEXTCOLOR', (0, 0), (-1, 0), colors.whitesmoke),
                ('ALIGN', (0, 0), (-1, -1), 'CENTER'),
                ('FONTNAME', (0, 0), (-1, 0), 'Helvetica-Bold'),
                ('BOTTOMPADDING', (0, 0), (-1, 0), 12),
                ('BACKGROUND', (0, 1), (-1, -1), colors.beige),
                ('GRID', (0, 0), (-1, -1), 1, colors.black)
            ]))
            elements.append(device_table)
            elements.append(Spacer(1, 12))

        # Vulnerabilidades
        if self.vulnerabilities:
            vuln_info = []
            for vuln in self.vulnerabilities:
                color = colors.red if vuln['severity'].lower() == 'high' else colors.black
                vuln_info.append([Paragraph(vuln['type'], styles['Normal']), Paragraph(vuln['severity'].upper(), styles['Normal']), Paragraph(vuln['description'], styles['Normal']), Paragraph(vuln.get('details', 'N/A'), styles['Normal'])])
            vuln_table = Table([["Tipo", "Severidad", "Descripción", "Detalles"]] + vuln_info)
            vuln_table.setStyle(TableStyle([
                ('BACKGROUND', (0, 0), (-1, 0), colors.grey),
                ('TEXTCOLOR', (0, 0), (-1, 0), colors.whitesmoke),
                ('ALIGN', (0, 0), (-1, -1), 'CENTER'),
                ('FONTNAME', (0, 0), (-1, 0), 'Helvetica-Bold'),
                ('BOTTOMPADDING', (0, 0), (-1, 0), 12),
                ('BACKGROUND', (0, 1), (-1, -1), colors.beige),
                ('GRID', (0, 0), (-1, -1), 1, colors.black)
            ]))
            elements.append(vuln_table)
            elements.append(Spacer(1, 12))

        # Recomendaciones de IA
        if ai_recommendations:
            elements.append(Paragraph("=== Recomendaciones de IA ===", styles['Heading2']))
            elements.append(Paragraph(ai_recommendations, styles['Normal']))
            elements.append(Spacer(1, 12))

        # Generar PDF
        pdf = SimpleDocTemplate(pdf_filename, pagesize=letter)
        pdf.build(elements)
        print(f"\nReporte guardado en: {pdf_filename}")

    def setup_database(self):
        """Configura la base de datos si no existe"""
        try:
            # Primero conectar sin especificar base de datos
            conn = mysql.connector.connect(
                host=DB_CONFIG['host'],
                user=DB_CONFIG['user'],
                password=DB_CONFIG['password']
            )
            cursor = conn.cursor()
            
            # Crear la base de datos si no existe
            cursor.execute(f"CREATE DATABASE IF NOT EXISTS {DB_CONFIG['database']}")
            cursor.execute(f"USE {DB_CONFIG['database']}")
            
            # Crear tablas necesarias
            cursor.execute("""
            CREATE TABLE IF NOT EXISTS scans (
                scan_id INT AUTO_INCREMENT PRIMARY KEY,
                scan_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                scan_duration FLOAT,
                total_devices INT,
                total_vulnerabilities INT
            )
            """)
            
            cursor.execute("""
            CREATE TABLE IF NOT EXISTS wifi_networks (
                network_id INT AUTO_INCREMENT PRIMARY KEY,
                scan_id INT,
                ssid VARCHAR(100),
                authentication VARCHAR(50),
                encryption VARCHAR(50),
                `signal` VARCHAR(20),
                security_key VARCHAR(255),
                FOREIGN KEY (scan_id) REFERENCES scans(scan_id) ON DELETE CASCADE
            )
            """)
            
            cursor.execute("""
            CREATE TABLE IF NOT EXISTS devices (
                device_id INT AUTO_INCREMENT PRIMARY KEY,
                scan_id INT,
                ip_address VARCHAR(15),
                hostname VARCHAR(100),
                last_seen DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (scan_id) REFERENCES scans(scan_id) ON DELETE CASCADE
            )
            """)
            
            cursor.execute("""
            CREATE TABLE IF NOT EXISTS open_ports (
                port_id INT AUTO_INCREMENT PRIMARY KEY,
                device_id INT,
                port_number INT,
                service_name VARCHAR(50),
                banner TEXT,
                FOREIGN KEY (device_id) REFERENCES devices(device_id) ON DELETE CASCADE
            )
            """)
            
            cursor.execute("""
            CREATE TABLE IF NOT EXISTS vulnerabilities (
                vuln_id INT AUTO_INCREMENT PRIMARY KEY,
                scan_id INT,
                vuln_type VARCHAR(50),
                severity VARCHAR(20),
                description TEXT,
                affected_device VARCHAR(15),
                details TEXT,
                FOREIGN KEY (scan_id) REFERENCES scans(scan_id) ON DELETE CASCADE
            )
            """)
            
            cursor.execute("""
            CREATE TABLE IF NOT EXISTS ai_recommendations (
                rec_id INT AUTO_INCREMENT PRIMARY KEY,
                scan_id INT,
                recommendation TEXT,
                FOREIGN KEY (scan_id) REFERENCES scans(scan_id) ON DELETE CASCADE
            )
            """)
            
            conn.commit()
            print("Base de datos configurada correctamente.")
            return True
        except mysql.connector.Error as err:
            print(f"Error al configurar la base de datos: {err}")
            return False
        finally:
            if 'conn' in locals() and conn.is_connected():
                cursor.close()
                conn.close()
                
    def save_to_database(self, ai_recommendations, scan_time):
        """Guarda toda la información en la base de datos"""
        try:
            conn = mysql.connector.connect(**DB_CONFIG)
            cursor = conn.cursor()
            
            # 1. Guardar información del escaneo
            cursor.execute("""
            INSERT INTO scans (scan_date, scan_duration, total_devices, total_vulnerabilities) 
            VALUES (%s, %s, %s, %s)
            """, (
                datetime.now().strftime('%Y-%m-%d %H:%M:%S'),
                scan_time,
                len(self.devices),
                len(self.vulnerabilities)
            ))
            
            # Obtener el ID del escaneo
            self.scan_id = cursor.lastrowid
            
            # 2. Guardar información de la red WiFi
            if self.network_info:
                cursor.execute("""
                INSERT INTO wifi_networks (scan_id, ssid, authentication, encryption, `signal`, security_key)
                VALUES (%s, %s, %s, %s, %s, %s)
                """, (
                    self.scan_id,
                    self.network_info.get('ssid', 'Unknown'),
                    self.network_info.get('authentication', 'Unknown'),
                    self.network_info.get('encryption', 'Unknown'),
                    self.network_info.get('signal', 'Unknown'),
                    self.network_info.get('details', {}).get('security_key', 'Unknown')
                ))
            
            # 3. Guardar dispositivos y puertos
            for device in self.devices:
                ip, ports_info = device
                
                # Guardar dispositivo
                cursor.execute("""
                INSERT INTO devices (scan_id, ip_address, hostname, last_seen)
                VALUES (%s, %s, %s, %s)
                """, (
                    self.scan_id,
                    ip,
                    # Intentar obtener hostname
                    self.get_hostname(ip),
                    datetime.now().strftime('%Y-%m-%d %H:%M:%S')
                ))
                
                device_id = cursor.lastrowid
                
                # Guardar puertos abiertos
                for port_info in ports_info:
                    _, port, service, banner = port_info
                    cursor.execute("""
                    INSERT INTO open_ports (device_id, port_number, service_name, banner)
                    VALUES (%s, %s, %s, %s)
                    """, (
                        device_id,
                        port,
                        service,
                        banner if banner else None
                    ))
            
            # 4. Guardar vulnerabilidades
            for vuln in self.vulnerabilities:
                cursor.execute("""
                INSERT INTO vulnerabilities (scan_id, vuln_type, severity, description, affected_device, details)
                VALUES (%s, %s, %s, %s, %s, %s)
                """, (
                    self.scan_id,
                    vuln['type'],
                    vuln['severity'],
                    vuln['description'],
                    vuln.get('device', 'Unknown'),
                    vuln.get('details', None)
                ))
            
            # 5. Guardar recomendaciones de IA
            if ai_recommendations:
                cursor.execute("""
                INSERT INTO ai_recommendations (scan_id, recommendation)
                VALUES (%s, %s)
                """, (
                    self.scan_id,
                    ai_recommendations
                ))
            
            conn.commit()
            print(f"Información guardada en la base de datos con ID de escaneo: {self.scan_id}")
            return True
        except mysql.connector.Error as err:
            print(f"Error al guardar en la base de datos: {err}")
            return False
        finally:
            if 'conn' in locals() and conn.is_connected():
                cursor.close()
                conn.close()
    
    def get_hostname(self, ip):
        """Intenta obtener el hostname de una IP"""
        try:
            return socket.gethostbyaddr(ip)[0]
        except:
            return 'Unknown'
                
    def generate_report(self, ai_recommendations, scan_time):
        """Genera un reporte completo"""
        # Guardar en la base de datos
        self.save_to_database(ai_recommendations, scan_time)
        
        # Generar PDF
        self.generate_pdf_report(ai_recommendations, scan_time)

def main():
    # Ya no necesitamos obtener la API key de las variables de entorno
    scanner = NetworkSecuritySuite()
    scanner.scan_network()

if __name__ == "__main__":
    main()
