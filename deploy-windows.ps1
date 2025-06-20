# 🚀 Script de Despliegue para Windows - Puntos Estilo
# Autor: Octavio Buitrago
# Fecha: $(Get-Date)

Write-Host "🎉 Iniciando despliegue de Puntos Estilo a GitHub..." -ForegroundColor Green

# Verificar si estamos en el directorio correcto
if (-not (Test-Path "README.md")) {
    Write-Host "❌ No se encontró README.md. Asegúrate de estar en el directorio raíz del proyecto." -ForegroundColor Red
    exit 1
}

Write-Host "✅ Verificando directorio del proyecto..." -ForegroundColor Blue

# Verificar si Git está instalado
try {
    $gitVersion = git --version
    Write-Host "✅ Git encontrado: $gitVersion" -ForegroundColor Green
} catch {
    Write-Host "❌ Git no está instalado. Por favor instala Git desde https://git-scm.com/" -ForegroundColor Red
    Write-Host "📋 Después de instalar Git, ejecuta este script nuevamente." -ForegroundColor Yellow
    exit 1
}

# 1. Inicializar Git (si no está inicializado)
if (-not (Test-Path ".git")) {
    Write-Host "📁 Inicializando repositorio Git..." -ForegroundColor Blue
    git init
    Write-Host "✅ Repositorio Git inicializado" -ForegroundColor Green
} else {
    Write-Host "✅ Repositorio Git ya existe" -ForegroundColor Green
}

# 2. Agregar archivos al staging
Write-Host "📦 Agregando archivos al staging..." -ForegroundColor Blue
git add .
Write-Host "✅ Archivos agregados al staging" -ForegroundColor Green

# 3. Hacer commit inicial
Write-Host "💾 Realizando commit inicial..." -ForegroundColor Blue
$commitMessage = @"
🎉 Initial commit: Sistema Puntos Estilo completo

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

Desarrollado con ❤️ por Octavio Buitrago
"@

git commit -m $commitMessage
Write-Host "✅ Commit inicial realizado" -ForegroundColor Green

# 4. Verificar si ya existe remote origin
try {
    $remoteUrl = git remote get-url origin
    Write-Host "✅ Remote origin ya existe: $remoteUrl" -ForegroundColor Green
} catch {
    Write-Host "⚠️ No se encontró remote origin." -ForegroundColor Yellow
    Write-Host "📋 Debes configurarlo manualmente:" -ForegroundColor Yellow
    Write-Host "   git remote add origin https://github.com/Octaviusb/puntos-estilo.git" -ForegroundColor Cyan
}

# 5. Crear tag de versión
Write-Host "🏷️ Creando tag de versión v1.0.0..." -ForegroundColor Blue
git tag -a v1.0.0 -m "🎉 Primera versión estable de Puntos Estilo"
Write-Host "✅ Tag v1.0.0 creado" -ForegroundColor Green

# 6. Mostrar instrucciones finales
Write-Host ""
Write-Host "🎉 ¡Despliegue preparado exitosamente!" -ForegroundColor Green
Write-Host ""
Write-Host "📋 Próximos pasos:" -ForegroundColor Yellow
Write-Host "1. Ve a https://github.com/Octaviusb" -ForegroundColor Cyan
Write-Host "2. Crea un nuevo repositorio llamado 'puntos-estilo'" -ForegroundColor Cyan
Write-Host "3. NO inicialices con README (ya tienes uno)" -ForegroundColor Cyan
Write-Host "4. Ejecuta los siguientes comandos:" -ForegroundColor Cyan
Write-Host ""
Write-Host "   git remote add origin https://github.com/Octaviusb/puntos-estilo.git" -ForegroundColor White
Write-Host "   git branch -M main" -ForegroundColor White
Write-Host "   git push -u origin main" -ForegroundColor White
Write-Host "   git push origin v1.0.0" -ForegroundColor White
Write-Host ""
Write-Host "5. Ve a tu repositorio y crea un Release con el tag v1.0.0" -ForegroundColor Cyan
Write-Host ""
Write-Host "⚠️ Recuerda cambiar las credenciales de la base de datos antes de producción" -ForegroundColor Yellow
Write-Host ""
Write-Host "🌍 ¡Tu proyecto Puntos Estilo está listo para el mundo!" -ForegroundColor Green 