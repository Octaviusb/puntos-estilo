#!/bin/bash

# 🚀 Script de Despliegue Automático - Puntos Estilo
# Autor: Octavio Buitrago
# Fecha: $(date)

echo "🎉 Iniciando despliegue de Puntos Estilo a GitHub..."

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Función para imprimir mensajes
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Verificar si estamos en el directorio correcto
if [ ! -f "README.md" ]; then
    print_error "No se encontró README.md. Asegúrate de estar en el directorio raíz del proyecto."
    exit 1
fi

print_status "Verificando directorio del proyecto..."

# 1. Crear archivo .gitignore
print_status "Creando archivo .gitignore..."
cat > .gitignore << 'EOF'
# Archivos de configuración sensibles
config.php
database.sql
.env

# Logs
*.log
logs/

# Archivos temporales
*.tmp
*.temp

# Archivos de sistema
.DS_Store
Thumbs.db

# Dependencias
node_modules/
vendor/

# Archivos de backup
*.bak
*.backup

# Archivos de IDE
.vscode/
.idea/
*.swp
*.swo

# Archivos de upload
uploads/
img/avatars/

# Archivos de caché
cache/
*.cache
EOF
print_success "Archivo .gitignore creado"

# 2. Crear archivo de configuración de ejemplo
print_status "Creando archivo de configuración de ejemplo..."
mkdir -p carpeta/server
cat > carpeta/server/config.example.php << 'EOF'
<?php
// Configuración de ejemplo - NO usar en producción
$db_host = 'localhost';
$db_user = 'your_username';
$db_pass = 'your_password';
$db_name = 'your_database';

// Crear conexión
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

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
EOF
print_success "Archivo config.example.php creado"

# 3. Crear archivo LICENSE
print_status "Creando archivo LICENSE..."
cat > LICENSE << 'EOF'
MIT License

Copyright (c) 2024 Octavio Buitrago - Puntos Estilo

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
EOF
print_success "Archivo LICENSE creado"

# 4. Inicializar Git (si no está inicializado)
if [ ! -d ".git" ]; then
    print_status "Inicializando repositorio Git..."
    git init
    print_success "Repositorio Git inicializado"
else
    print_status "Repositorio Git ya existe"
fi

# 5. Agregar archivos al staging
print_status "Agregando archivos al staging..."
git add .
print_success "Archivos agregados al staging"

# 6. Hacer commit inicial
print_status "Realizando commit inicial..."
git commit -m "🎉 Initial commit: Sistema Puntos Estilo completo

✨ Características implementadas:
- Sistema de fidelización completo
- Panel administrativo intuitivo
- Autenticación segura con OTP
- Diseño responsive
- Catálogo de beneficios dinámico
- Reportes y analytics
- Seguridad reforzada
- Documentación completa

🔐 Seguridad:
- Prepared statements
- Hashing seguro de contraseñas
- Validación de entrada
- Protección CSRF
- Headers de seguridad

📱 Tecnologías:
- PHP 8.1
- MySQL
- HTML5/CSS3/JavaScript
- Bootstrap
- jQuery

Desarrollado con ❤️ por Octavio Buitrago"
print_success "Commit inicial realizado"

# 7. Verificar si ya existe remote origin
if git remote get-url origin > /dev/null 2>&1; then
    print_status "Remote origin ya existe"
else
    print_warning "No se encontró remote origin. Debes configurarlo manualmente:"
    echo "git remote add origin https://github.com/Octaviusb/puntos-estilo.git"
fi

# 8. Crear tag de versión
print_status "Creando tag de versión v1.0.0..."
git tag -a v1.0.0 -m "🎉 Primera versión estable de Puntos Estilo"
print_success "Tag v1.0.0 creado"

# 9. Mostrar instrucciones finales
echo ""
print_success "¡Despliegue preparado exitosamente!"
echo ""
echo "📋 Próximos pasos:"
echo "1. Ve a https://github.com/Octaviusb"
echo "2. Crea un nuevo repositorio llamado 'puntos-estilo'"
echo "3. NO inicialices con README (ya tienes uno)"
echo "4. Ejecuta los siguientes comandos:"
echo ""
echo "   git remote add origin https://github.com/Octaviusb/puntos-estilo.git"
echo "   git branch -M main"
echo "   git push -u origin main"
echo "   git push origin v1.0.0"
echo ""
echo "5. Ve a tu repositorio y crea un Release con el tag v1.0.0"
echo ""
print_warning "Recuerda cambiar las credenciales de la base de datos antes de producción"
echo ""
print_success "¡Tu proyecto Puntos Estilo está listo para el mundo! 🌍" 