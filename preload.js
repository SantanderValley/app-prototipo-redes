const { contextBridge, ipcRenderer } = require('electron');

// Exponer API segura a través del contexto aislado
contextBridge.exposeInMainWorld('electronAPI', {
  // Función para ejecutar scripts Python
  executePythonScript: (scriptName, args) => {
    return ipcRenderer.invoke('execute-python-script', scriptName, args);
  },
  
  // Mostrar notificaciones
  showNotification: (title, body) => {
    new Notification(title, { body });
  }
});

// Agregar funcionalidad para modificar el DOM y manejar la integración PHP-Electron
window.addEventListener('DOMContentLoaded', () => {
  // Interceptar los formularios para manejar la ejecución del escáner desde PHP
  document.addEventListener('click', (event) => {
    const scanButton = event.target.closest('#scan-button, .scan-network-btn, button[data-action="scan"]');
    
    if (scanButton) {
      // Si es el botón de escanear, podemos interceptarlo
      console.log('Botón de escaneo detectado, permitiendo comportamiento normal');
      // No prevenimos el comportamiento por defecto para permitir que el código PHP funcione
    }
  });
  
  // Inyectar información del entorno de Electron
  const injectElectronInfo = () => {
    const infoElement = document.createElement('div');
    infoElement.style.position = 'fixed';
    infoElement.style.bottom = '10px';
    infoElement.style.left = '10px';
    infoElement.style.fontSize = '10px';
    infoElement.style.color = '#999';
    infoElement.style.zIndex = '9999';
    infoElement.innerHTML = 'Ejecutando en Electron';
    document.body.appendChild(infoElement);
  };
  
  setTimeout(injectElectronInfo, 2000);
});
