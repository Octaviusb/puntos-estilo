# 🎯 Puntos Estilo - Sistema de Fidelización

[![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)](https://developer.mozilla.org/en-US/docs/Web/HTML)
[![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)](https://developer.mozilla.org/en-US/docs/Web/CSS)
[![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg?style=for-the-badge)](https://opensource.org/licenses/MIT)

## 📋 Descripción

**Puntos Estilo** es un sistema completo de fidelización desarrollado en PHP que permite a las empresas gestionar programas de lealtad de manera eficiente y moderna. El sistema incluye un panel administrativo intuitivo, autenticación segura, catálogo de beneficios dinámico y reportes avanzados.

## ✨ Características Principales

### 🔐 **Sistema de Autenticación**
- Login con email y contraseña
- Autenticación de dos factores (OTP)
- Registro de usuarios
- Recuperación de contraseña
- Control de sesiones
- Roles de usuario (admin/usuario)

### 👤 **Panel de Usuario**
- Perfil personalizable con avatar
- Visualización de puntos acumulados
- Historial de transacciones
- Mis consumos y bonos
- Sistema de referidos
- Retos y desafíos

### ⚙️ **Panel Administrativo**
- Gestión de usuarios
- Gestión de productos/beneficios
- Gestión de canjes
- Reportes y analytics
- Carga masiva de puntos (CSV)
- Configuración del sistema

### 🎁 **Sistema de Puntos**
- Acumulación automática
- Redención de beneficios
- Catálogo dinámico
- Control de stock
- Sistema de tickets
- Vencimiento de puntos

## 🚀 Instalación

### Requisitos Previos
- PHP 8.1 o superior
- MySQL 5.7 o superior
- Servidor web (Apache/Nginx)
- Composer (opcional)

### Pasos de Instalación

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/Octaviusb/puntos-estilo.git
   cd puntos-estilo
   ```

2. **Configurar la base de datos**
   ```bash
   # Importar la estructura de la base de datos
   mysql -u root -p < carpeta/db/mi_proyecto.sql
   ```

3. **Configurar las credenciales**
   ```bash
   # Copiar el archivo de configuración de ejemplo
   cp carpeta/server/config.example.php carpeta/server/config.php
   
   # Editar las credenciales en config.php
   nano carpeta/server/config.php
   ```

4. **Configurar el servidor web**
   - Apuntar el DocumentRoot a la carpeta `carpeta/frontend/`
   - Asegurar que PHP tenga permisos de escritura en `img/avatars/`

5. **Acceder al sistema**
   - URL: `http://localhost/`
   - Usuario admin por defecto: `admin@puntosestilo.com`
   - Contraseña: `admin123`

## 📁 Estructura del Proyecto

```
PuntosEstilo/
├── carpeta/
│   ├── frontend/           # Interfaz de usuario
│   │   ├── css/           # Estilos CSS
│   │   ├── js/            # JavaScript
│   │   ├── img/           # Imágenes
│   │   ├── includes/      # Archivos PHP incluidos
│   │   └── pages/         # Páginas adicionales
│   ├── server/            # Lógica del servidor
│   └── db/                # Archivos de base de datos
├── tienda/                # Módulo de tienda
├── includes/              # Archivos compartidos
└── docs/                  # Documentación
```

## 🔧 Configuración

### Variables de Entorno
```php
// carpeta/server/config.php
$db_host = 'localhost';
$db_user = 'your_username';
$db_pass = 'your_password';
$db_name = 'your_database';
```

### Configuración de Seguridad
- Cambiar credenciales por defecto
- Configurar HTTPS en producción
- Habilitar firewall
- Configurar backup automático

## 📊 Base de Datos

### Tablas Principales
- `usuarios` - Información de usuarios
- `transacciones` - Historial de puntos
- `productos` - Catálogo de beneficios
- `canjes` - Registro de redenciones
- `retos` - Sistema de desafíos
- `referidos` - Sistema de referencias
- `codigos_otp` - Autenticación de dos factores

## 🎨 Personalización

### Colores del Tema
```css
/* Colores principales */
--primary-color: #2c3e50;    /* Azul oscuro */
--secondary-color: #3498db;  /* Azul claro */
--accent-color: #e74c3c;     /* Rojo */
--success-color: #27ae60;    /* Verde */
--warning-color: #f39c12;    /* Naranja */
```

### Modificar Estilos
Los estilos principales se encuentran en:
- `carpeta/frontend/css/unified-styles.css`
- `carpeta/frontend/css/styles.css`

## 🔐 Seguridad

### Medidas Implementadas
- ✅ Hashing seguro de contraseñas (password_hash)
- ✅ Prepared statements para prevenir SQL injection
- ✅ Validación de entrada en todos los formularios
- ✅ Sanitización de datos con real_escape_string
- ✅ Control de sesiones con timeout
- ✅ Validación de roles y permisos
- ✅ Sistema OTP para autenticación adicional

### Recomendaciones de Producción
- Cambiar credenciales por defecto
- Habilitar HTTPS obligatorio
- Configurar rate limiting
- Implementar logging de seguridad
- Realizar auditorías regulares

## 📈 Reportes y Analytics

### Métricas Disponibles
- Usuarios registrados
- Puntos totales en el sistema
- Transacciones realizadas
- Productos más populares
- Tasa de conversión
- Actividad reciente

### Dashboard Administrativo
- Gráficos en tiempo real
- Filtros por fecha
- Exportación de datos
- Alertas automáticas

## 🤝 Contribución

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📝 Licencia

Este proyecto está bajo la Licencia MIT - ver el archivo [LICENSE](LICENSE) para más detalles.

## 👨‍💻 Desarrollador

**Octavio Buitrago** - [@Octaviusb](https://github.com/Octaviusb)

- 📧 Email: obuitragocamelo@yahoo.es
- 🌍 Ubicación: Villavicencio, Meta, Colombia
- 💼 LinkedIn: [Octavio Buitrago](https://linkedin.com/in/octavio-buitrago)

## 🙏 Agradecimientos

- Bootstrap por el framework CSS
- jQuery por la librería JavaScript
- Font Awesome por los iconos
- La comunidad PHP por el soporte

## 📞 Soporte

Si tienes alguna pregunta o necesitas ayuda:

- 📧 Email: soporte@puntosestilo.com
- 📱 Teléfono: +57 555-555-555
- 🌐 Website: puntosestilo.com (próximamente)

---

⭐ **¡Si te gusta este proyecto, dale una estrella en GitHub!**

**Desarrollado con ❤️ por Octavio Buitrago** 