# 🚀 Instrucciones Rápidas - Puntos Estilo

## ⚡ Inicio Rápido

### 1. Verificar Instalación
Abre en tu navegador:
```
http://localhost/PuntosEstilo/verificar_instalacion.php
```

Este script verificará automáticamente que todo esté funcionando correctamente.

### 2. Acceder al Sistema
URL del sistema:
```
http://localhost/PuntosEstilo/frontend/
```

### 3. Credenciales por Defecto
- **Email**: `admin@puntosestilo.com`
- **Contraseña**: `password`
- **Rol**: Administrador

## 🔐 Primer Login

1. **Ingresa las credenciales** en la página de login
2. **Haz clic en "Solicitar OTP"**
3. **Revisa los logs de error** de PHP para ver el código OTP
4. **Ingresa el código OTP** de 6 dígitos
5. **Haz clic en "Validar OTP e Ingresar"**

### 📍 Encontrar el OTP (Desarrollo)
- **XAMPP**: `C:\xampp\apache\logs\error.log`
- **WAMP**: `C:\wamp64\logs\apache_error.log`
- **Linux**: `/var/log/apache2/error.log`

Busca una línea como: `OTP para admin@puntosestilo.com: 123456`

## 🎯 Funcionalidades Principales

### Para Administradores
- **Dashboard**: Panel principal con estadísticas
- **Gestión de Usuarios**: Ver, editar y gestionar usuarios
- **Gestión de Productos**: Agregar productos al catálogo
- **Gestión de Canjes**: Aprobar/rechazar solicitudes
- **Reportes**: Estadísticas del sistema
- **Carga Masiva**: Importar puntos desde CSV

### Para Usuarios
- **Perfil**: Ver y editar información personal
- **Mis Puntos**: Consultar saldo y transacciones
- **Catálogo**: Ver productos disponibles
- **Mis Canjes**: Historial de canjes
- **Mensajes**: Comunicación con administradores

## 🔧 Configuración Importante

### Cambiar Contraseña de Administrador
1. Accede al sistema como administrador
2. Ve a "Mi Perfil"
3. Cambia la contraseña por defecto

### Configurar Email (Producción)
Edita `server/config.php`:
```php
// Configuración de email
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'tu-email@gmail.com');
define('SMTP_PASS', 'tu-contraseña-de-aplicación');
```

## 📁 Estructura de Archivos Importantes

```
PuntosEstilo/
├── frontend/                 # Interfaz de usuario
│   ├── login.php            # Página de login
│   ├── dashboard.php        # Panel principal
│   ├── css/login.css        # Estilos del login
│   └── js/login-otp.js      # JavaScript del login
├── server/
│   ├── config.php           # Configuración principal
│   └── create_tables.sql    # Script de base de datos
└── verificar_instalacion.php # Script de verificación
```

## 🐛 Solución de Problemas Comunes

### Error de Conexión a Base de Datos
1. Verifica que MySQL esté ejecutándose
2. Revisa las credenciales en `server/config.php`
3. Asegúrate de que la base de datos `mi_proyecto` existe

### OTP No Funciona
1. **En desarrollo**: Revisa los logs de error
2. **En producción**: Verifica configuración SMTP
3. **Alternativa**: Usa el login tradicional sin OTP

### Página en Blanco
1. Habilita errores temporalmente en `server/config.php`:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Estilos No Se Cargan
1. Verifica que `frontend/css/login.css` existe
2. Revisa la consola del navegador para errores
3. Verifica permisos de archivos

## 📊 Pruebas Recomendadas

### Como Administrador
1. ✅ Crear un usuario nuevo
2. ✅ Agregar puntos manualmente
3. ✅ Crear un producto en el catálogo
4. ✅ Aprobar un canje
5. ✅ Generar un reporte

### Como Usuario
1. ✅ Registrarse en el sistema
2. ✅ Verificar puntos recibidos
3. ✅ Solicitar un canje
4. ✅ Enviar un mensaje
5. ✅ Actualizar perfil

## 🔒 Seguridad

### Cambios Obligatorios para Producción
1. **Cambiar contraseña** de administrador
2. **Configurar HTTPS**
3. **Configurar envío de emails**
4. **Cambiar credenciales** de base de datos
5. **Configurar backup** automático

### Configuración de Seguridad
- Todas las consultas usan prepared statements
- Contraseñas hasheadas con bcrypt
- Validación de entrada en todos los formularios
- Protección CSRF implementada
- Headers de seguridad configurados

## 📞 Soporte

### Información de Contacto
- **Email**: soporte@puntosestilo.com
- **Documentación**: Ver `INSTALACION.md`
- **Verificación**: Usar `verificar_instalacion.php`

### Logs Importantes
- **Errores PHP**: Logs del servidor web
- **Errores MySQL**: Logs de MySQL
- **Acceso**: Logs de Apache/Nginx

## 🎉 ¡Listo para Usar!

El sistema está completamente funcional y listo para:
- ✅ Gestionar usuarios y puntos
- ✅ Administrar catálogo de productos
- ✅ Procesar canjes y transacciones
- ✅ Generar reportes y estadísticas
- ✅ Comunicación interna

**¡Disfruta usando Puntos Estilo!** 🚀 