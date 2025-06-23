# 🚀 Guía de Instalación - Puntos Estilo

## 📋 Requisitos del Sistema

### Requisitos Mínimos
- **Servidor Web**: Apache 2.4+ o Nginx 1.18+
- **PHP**: 7.4 o superior
- **MySQL**: 5.7 o superior / MariaDB 10.2+
- **Memoria RAM**: 512MB mínimo
- **Espacio en disco**: 100MB

### Extensiones PHP Requeridas
```bash
- mysqli
- session
- json
- mbstring
- fileinfo
- gd (para procesamiento de imágenes)
- curl (para envío de emails)
```

## 🛠️ Instalación Paso a Paso

### 1. Preparación del Entorno

#### Para XAMPP (Windows/Linux/macOS)
1. Descarga e instala XAMPP desde https://www.apachefriends.org/
2. Inicia Apache y MySQL desde el panel de control
3. Navega a `http://localhost/phpmyadmin`
4. Crea una nueva base de datos llamada `mi_proyecto`

#### Para WAMP (Windows)
1. Descarga e instala WAMP desde https://www.wampserver.com/
2. Inicia los servicios
3. Accede a phpMyAdmin y crea la base de datos

#### Para LAMP (Linux)
```bash
sudo apt update
sudo apt install apache2 mysql-server php php-mysql php-mbstring php-json php-gd php-curl
sudo systemctl start apache2 mysql
```

### 2. Configuración del Proyecto

1. **Clona o descarga el proyecto**:
```bash
cd /var/www/html/  # Linux
# o
cd C:/xampp/htdocs/  # Windows XAMPP
# o
cd C:/wamp64/www/    # Windows WAMP

git clone [URL_DEL_REPOSITORIO] PuntosEstilo
cd PuntosEstilo
```

2. **Configura permisos** (Linux):
```bash
sudo chown -R www-data:www-data /var/www/html/PuntosEstilo
sudo chmod -R 755 /var/www/html/PuntosEstilo
sudo chmod -R 777 /var/www/html/PuntosEstilo/frontend/img/
sudo chmod -R 777 /var/www/html/PuntosEstilo/frontend/uploads/
```

### 3. Configuración de Base de Datos

1. **Accede a phpMyAdmin**:
   - URL: `http://localhost/phpmyadmin`
   - Usuario: `root`
   - Contraseña: (vacía por defecto en XAMPP)

2. **Crea la base de datos**:
```sql
CREATE DATABASE mi_proyecto CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

3. **Importa las tablas**:
   - Ve a la pestaña "Importar"
   - Selecciona el archivo `server/create_tables.sql`
   - Haz clic en "Continuar"

### 4. Configuración de Conexión

1. **Edita el archivo de configuración**:
```bash
nano server/config.php
```

2. **Actualiza las credenciales**:
```php
<?php
// Configuración de base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');           // Tu usuario MySQL
define('DB_PASS', '');               // Tu contraseña MySQL
define('DB_NAME', 'mi_proyecto');    // Nombre de la base de datos

// Conexión a la base de datos
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Configurar charset
$conn->set_charset("utf8");

// Configuración de la sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuración de zona horaria
date_default_timezone_set('America/Bogota');

// Configuración de errores (deshabilitar en producción)
error_reporting(0);
ini_set('display_errors', 0);

// Función para sanitizar datos
function sanitize($data) {
    global $conn;
    return $conn->real_escape_string(trim($data));
}

// Función para verificar si el usuario está logueado
function isLoggedIn() {
    return isset($_SESSION['user']) && !empty($_SESSION['user']);
}

// Función para verificar si el usuario es administrador
function isAdmin() {
    return isset($_SESSION['user']['rol']) && $_SESSION['user']['rol'] === 'admin';
}

// Función para redirigir
function redirect($url) {
    header("Location: $url");
    exit();
}
?>
```

### 5. Verificación de la Instalación

1. **Accede al sistema**:
   - URL: `http://localhost/PuntosEstilo/frontend/`
   - Deberías ver la página de login

2. **Credenciales por defecto**:
   - **Email**: `admin@puntosestilo.com`
   - **Contraseña**: `password`
   - **Rol**: Administrador

3. **Prueba el login**:
   - Ingresa las credenciales
   - El sistema generará un OTP (revisa los logs de error para verlo)
   - Completa el login con el OTP

## 🔧 Configuración Avanzada

### Configuración de Email (Producción)

1. **Edita la configuración de email** en `server/config.php`:
```php
// Configuración de email
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'tu-email@gmail.com');
define('SMTP_PASS', 'tu-contraseña-de-aplicación');
define('SMTP_FROM', 'noreply@puntosestilo.com');
define('SMTP_FROM_NAME', 'Puntos Estilo');
```

2. **Para Gmail**:
   - Habilita la verificación en dos pasos
   - Genera una contraseña de aplicación
   - Usa esa contraseña en SMTP_PASS

### Configuración de Seguridad

1. **Archivo .htaccess** (ya incluido):
```apache
# Protección de archivos sensibles
<Files "config.php">
    Order allow,deny
    Deny from all
</Files>

# Headers de seguridad
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
```

2. **Configuración de sesiones**:
```php
// En server/config.php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); // Solo si usas HTTPS
ini_set('session.use_strict_mode', 1);
```

### Optimización de Rendimiento

1. **Habilita caché de PHP**:
```php
// En server/config.php
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
```

2. **Configuración de MySQL**:
```sql
-- En my.cnf o my.ini
[mysqld]
innodb_buffer_pool_size = 256M
query_cache_size = 64M
query_cache_type = 1
```

## 🐛 Solución de Problemas

### Error de Conexión a Base de Datos
```bash
# Verifica que MySQL esté ejecutándose
sudo systemctl status mysql  # Linux
# o revisa el panel de XAMPP/WAMP

# Verifica las credenciales
mysql -u root -p
```

### Error de Permisos
```bash
# Linux - Verifica permisos
ls -la /var/www/html/PuntosEstilo/

# Corrige permisos si es necesario
sudo chown -R www-data:www-data /var/www/html/PuntosEstilo/
sudo chmod -R 755 /var/www/html/PuntosEstilo/
```

### OTP No Funciona
1. **En desarrollo**: Revisa los logs de error de PHP
2. **En producción**: Verifica la configuración SMTP
3. **Logs de error**: `/var/log/apache2/error.log` (Linux)

### Página en Blanco
1. **Habilita errores temporalmente**:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

2. **Verifica la sintaxis PHP**:
```bash
php -l server/config.php
```

## 📊 Verificación Post-Instalación

### Checklist de Verificación
- [ ] El sistema carga sin errores
- [ ] Puedes hacer login con las credenciales por defecto
- [ ] El OTP se genera correctamente
- [ ] Puedes navegar por todas las secciones
- [ ] Las imágenes se cargan correctamente
- [ ] Los formularios funcionan
- [ ] La base de datos tiene todas las tablas

### Pruebas Recomendadas
1. **Crear un usuario nuevo**
2. **Agregar puntos manualmente**
3. **Crear un producto en el catálogo**
4. **Solicitar un canje**
5. **Generar un reporte**

## 🔄 Actualizaciones

### Actualizar el Sistema
```bash
# Backup de la base de datos
mysqldump -u root -p mi_proyecto > backup_$(date +%Y%m%d).sql

# Actualizar archivos
git pull origin main

# Ejecutar migraciones si las hay
mysql -u root -p mi_proyecto < server/migrations/latest.sql
```

## 📞 Soporte Técnico

### Información de Contacto
- **Email**: soporte@puntosestilo.com
- **Documentación**: [URL de la documentación]
- **Issues**: [URL del repositorio de issues]

### Información del Sistema
- **Versión**: 1.0.0
- **Última actualización**: $(date)
- **Compatibilidad**: PHP 7.4+, MySQL 5.7+

---

**¡El sistema está listo para usar!** 🎉

Recuerda cambiar la contraseña del administrador después del primer login. 