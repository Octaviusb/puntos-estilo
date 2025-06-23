# ===================================================================
# CREADOR DE VIDEOS DESDE IMAGENES - PUNTOS ESTILO
# Autor: Gemini (Asistente de IA)
# Fecha: $(Get-Date)
#
# Proposito:
# Este script automatiza la creacion de un video de presentacion
# a partir de una secuencia de imagenes (PNG, JPG) usando FFmpeg.
# ===================================================================

# --- CONFIGURACION ---
$CarpetaImagenes = "frontend/img"
$FormatoImagenes = "Imagen*.png" # Cambia esto si tus imagenes se llaman diferente (p.ej., "Imagen*.jpg")
$DuracionPorImagen = 3  # Segundos que cada imagen aparecera en pantalla
$ArchivoSalida = "presentacion_puntos_estilo.mp4"
$CarpetaMusica = "video-production/music"
$ArchivoMusica = "background-music.mp3" # Opcional: Pon tu archivo de musica aqui

# --- FUNCION PARA VERIFICAR FFMPEG ---
function Test-FFmpeg {
    # Primero intentar con ffmpeg en PATH
    if (Get-Command ffmpeg -ErrorAction SilentlyContinue) {
        return "ffmpeg"
    }
    
    # Buscar en rutas comunes
    $rutasComunes = @(
        "ffmpeg\bin\ffmpeg.exe",
        "ffmpeg.exe",
        "C:\ffmpeg\bin\ffmpeg.exe",
        "$env:USERPROFILE\ffmpeg\bin\ffmpeg.exe"
    )
    
    foreach ($ruta in $rutasComunes) {
        if (Test-Path $ruta) {
            return $ruta
        }
    }
    
    return $null
}

# --- VERIFICACION DE FFMPEG ---
Write-Output "Verificando FFmpeg..."
$FFmpegPath = Test-FFmpeg

if (-not $FFmpegPath) {
    Write-Output "ERROR: FFmpeg no esta instalado."
    Write-Output ""
    Write-Output "OPCIONES PARA INSTALAR FFMPEG:"
    Write-Output "1. Descarga manual: https://ffmpeg.org/download.html"
    Write-Output "2. Usa Chocolatey: choco install ffmpeg"
    Write-Output "3. Usa Winget: winget install ffmpeg"
    Write-Output ""
    Write-Output "O coloca el ejecutable ffmpeg.exe en la raiz de este proyecto."
    exit 1
}

Write-Output "FFmpeg detectado en: $FFmpegPath"
Write-Output "Procediendo con la creacion del video..."

# --- VERIFICACION DE CARPETA DE IMAGENES ---
Write-Output "Verificando carpeta de imagenes: $CarpetaImagenes"
if (-not (Test-Path $CarpetaImagenes)) {
    Write-Output "ERROR: La carpeta '$CarpetaImagenes' no existe."
    Write-Output "   Por favor, crea la carpeta o ajusta la variable `$CarpetaImagenes."
    exit 1
}

# --- VERIFICACION DE IMAGENES ---
Write-Output "Buscando imagenes con patron: $FormatoImagenes"
$Imagenes = Get-ChildItem -Path $CarpetaImagenes -Filter $FormatoImagenes
if ($Imagenes.Count -eq 0) {
    Write-Output "ERROR: No se encontraron imagenes con el patron '$FormatoImagenes' en '$CarpetaImagenes'."
    Write-Output "   Imagenes disponibles en la carpeta:"
    Get-ChildItem -Path $CarpetaImagenes -File | ForEach-Object { Write-Output "   - $($_.Name)" }
    Write-Output "   Por favor, ajusta la variable `$FormatoImagenes o coloca las imagenes correctas."
    exit 1
}

Write-Output "Se encontraron $($Imagenes.Count) imagenes:"
$Imagenes | ForEach-Object { Write-Output "   - $($_.Name)" }

# --- LOGICA DEL SCRIPT ---

# 1. Crear un archivo de lista para FFmpeg
$ListaDeArchivos = "lista_imagenes.txt"
Write-Output "Creando lista de archivos..."

# Limpiar archivo anterior si existe
if (Test-Path $ListaDeArchivos) {
    Remove-Item $ListaDeArchivos -Force
}

$Imagenes | ForEach-Object {
    "file '$($_.FullName)'" | Add-Content $ListaDeArchivos
    "duration $DuracionPorImagen" | Add-Content $ListaDeArchivos
}

Write-Output "Lista de archivos creada: $ListaDeArchivos"

# 2. Construir el comando de FFmpeg
$ComandoFFmpeg = "`"$FFmpegPath`" -f concat -safe 0 -i $ListaDeArchivos"

# 3. (Opcional) Anadir musica si el archivo existe
$RutaMusicaCompleta = Join-Path -Path $CarpetaMusica -ChildPath $ArchivoMusica
if (Test-Path $RutaMusicaCompleta) {
    Write-Output "Archivo de musica detectado. Anadiendo '$ArchivoMusica' al video..."
    $ComandoFFmpeg += " -i `"$RutaMusicaCompleta`" -c:a aac -shortest"
} else {
    Write-Output "No se encontro archivo de musica en '$RutaMusicaCompleta'. El video se creara sin audio."
}

# Anadir opciones de video y el archivo de salida
$ComandoFFmpeg += " -c:v libx264 -r 30 -pix_fmt yuv420p -y $ArchivoSalida"

Write-Output "Comando FFmpeg que se ejecutara:"
Write-Output $ComandoFFmpeg

# 4. Ejecutar el comando final de FFmpeg
Write-Output "Ejecutando FFmpeg... Esto puede tardar unos momentos."
try {
    Invoke-Expression -Command $ComandoFFmpeg
    if ($LASTEXITCODE -eq 0) {
        Write-Output "¡EXITO! Tu video '$ArchivoSalida' ha sido creado."
        Write-Output "   Puedes encontrarlo en la carpeta raiz de tu proyecto."
    } else {
        Write-Output "ERROR: FFmpeg fallo con codigo de salida: $LASTEXITCODE"
    }
} catch {
    Write-Output "ERROR al ejecutar FFmpeg: $($_.Exception.Message)"
}

# 5. Limpieza de archivos temporales
Write-Output "Limpiando archivos temporales..."
Remove-Item $ListaDeArchivos -ErrorAction SilentlyContinue

# --- COMANDO FFMPEG DIRECTO ---
Write-Output "
COMANDO FFMPEG DIRECTO PARA CREAR TU VIDEO:

$FFmpegPath -framerate 1/3 -i frontend/img/Imagen%d.png -i video-production/music/musica.mp3 -c:v libx264 -r 30 -pix_fmt yuv420p -c:a aac -shortest presentacion_final.mp4
" 