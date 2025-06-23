# Script para organizar archivos de audio - Puntos Estilo
# Autor: Octavio Buitrago

Write-Host "🎵 Organizando archivos de audio..." -ForegroundColor Green

# Crear directorios si no existen
$directories = @(
    "audio/video1-demo",
    "audio/video2-promocional", 
    "audio/video3-tutorial"
)

foreach ($dir in $directories) {
    if (!(Test-Path $dir)) {
        New-Item -ItemType Directory -Path $dir -Force
        Write-Host "✅ Creado directorio: $dir" -ForegroundColor Yellow
    }
}

# Mapeo de archivos existentes a nombres descriptivos
$fileMapping = @{
    "_Los programas de fi.wav" = "video1-demo/01-intro.wav"
    "_Los programas de fi (1).wav" = "video1-demo/02-problema.wav"
    "_Los programas de fi (2).wav" = "video1-demo/03-solucion.wav"
    "_Los programas de fi (4).wav" = "video1-demo/04-demo-admin.wav"
    "_Los programas de fi (5).wav" = "video1-demo/05-demo-usuario.wav"
    "_Los programas de fi (6).wav" = "video1-demo/06-caracteristicas.wav"
    "_Los programas de fi (7).wav" = "video1-demo/07-beneficios.wav"
    "_Los programas de fi (8).wav" = "video1-demo/08-casos-exito.wav"
}

# Renombrar archivos
foreach ($oldFile in $fileMapping.Keys) {
    $oldPath = "audio/$oldFile"
    $newPath = "audio/$($fileMapping[$oldFile])"
    
    if (Test-Path $oldPath) {
        Move-Item -Path $oldPath -Destination $newPath -Force
        Write-Host "✅ Renombrado: $oldFile -> $($fileMapping[$oldFile])" -ForegroundColor Green
    } else {
        Write-Host "⚠️ No encontrado: $oldFile" -ForegroundColor Yellow
    }
}

Write-Host "`n📋 Archivos organizados:" -ForegroundColor Cyan
Get-ChildItem -Path "audio" -Recurse -Filter "*.wav" | ForEach-Object {
    Write-Host "  📁 $($_.FullName.Replace((Get-Location).Path + '\', ''))" -ForegroundColor White
}

Write-Host "`n🎯 Próximos pasos:" -ForegroundColor Magenta
Write-Host "1. Grabar pantalla del sistema (sin audio)" -ForegroundColor White
Write-Host "2. Instalar DaVinci Resolve" -ForegroundColor White
Write-Host "3. Sincronizar audio con video" -ForegroundColor White
Write-Host "4. Agregar música de fondo" -ForegroundColor White
Write-Host "5. Exportar video final" -ForegroundColor White

Write-Host "`n🚀 ¡Listo para continuar!" -ForegroundColor Green 