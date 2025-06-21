#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
🎤 Generador Automático de Audio con IA - Puntos Estilo
Autor: Octavio Buitrago
Fecha: 2024

Este script automatiza la generación de audio para los videos de Puntos Estilo
usando ElevenLabs API.
"""

import requests
import json
import os
import time
from pathlib import Path

class AudioGenerator:
    def __init__(self, api_key):
        self.api_key = api_key
        self.base_url = "https://api.elevenlabs.io/v1"
        self.headers = {
            "Accept": "audio/mpeg",
            "Content-Type": "application/json",
            "xi-api-key": api_key
        }
        
        # Crear directorios si no existen
        self.audio_dir = Path("audio")
        self.audio_dir.mkdir(exist_ok=True)
        
        for subdir in ["video1-demo", "video2-promocional", "video3-tutorial"]:
            (self.audio_dir / subdir).mkdir(exist_ok=True)

    def get_voices(self):
        """Obtener lista de voces disponibles"""
        url = f"{self.base_url}/voices"
        response = requests.get(url, headers=self.headers)
        
        if response.status_code == 200:
            voices = response.json()["voices"]
            print("🎤 Voces disponibles:")
            for voice in voices:
                print(f"  - {voice['name']} (ID: {voice['voice_id']})")
            return voices
        else:
            print(f"❌ Error al obtener voces: {response.status_code}")
            return []

    def generate_audio(self, text, voice_id, filename, voice_settings=None):
        """Generar audio con ElevenLabs"""
        url = f"{self.base_url}/text-to-speech/{voice_id}"
        
        # Configuración por defecto
        if voice_settings is None:
            voice_settings = {
                "stability": 0.5,
                "similarity_boost": 0.75,
                "style": 0.0,
                "use_speaker_boost": True
            }
        
        data = {
            "text": text,
            "model_id": "eleven_monolingual_v1",
            "voice_settings": voice_settings
        }
        
        print(f"🎵 Generando audio: {filename}")
        response = requests.post(url, json=data, headers=self.headers)
        
        if response.status_code == 200:
            filepath = self.audio_dir / filename
            with open(filepath, "wb") as f:
                f.write(response.content)
            print(f"✅ Audio guardado: {filepath}")
            return True
        else:
            print(f"❌ Error al generar audio: {response.status_code}")
            print(f"Respuesta: {response.text}")
            return False

    def generate_video1_audio(self, voice_id):
        """Generar audio para Video 1: Demo del Sistema"""
        print("\n🎬 Generando audio para Video 1: Demo del Sistema")
        
        scripts = {
            "video1-demo/01-intro.mp3": "Bienvenidos a Puntos Estilo, el sistema integral de fidelización que revoluciona la relación con tus clientes. En este video te mostraremos cómo transformar tu negocio con nuestra plataforma completa.",
            
            "video1-demo/02-problema.mp3": "Los programas de fidelización tradicionales presentan varios desafíos. Son complicados de implementar, costosos de mantener, difíciles de usar para los clientes y no proporcionan datos en tiempo real. Esto genera frustración tanto para las empresas como para sus clientes.",
            
            "video1-demo/03-solucion.mp3": "Puntos Estilo simplifica todo. Ofrecemos un sistema completo en una plataforma, fácil de implementar, con costo accesible y datos en tiempo real. Nuestra solución está diseñada para ser intuitiva y efectiva desde el primer día.",
            
            "video1-demo/04-demo-admin.mp3": "Como administrador, tienes control total sobre tu programa de fidelización. Puedes gestionar usuarios, configurar puntos, crear beneficios personalizados, ver reportes detallados y monitorear la actividad en tiempo real. Todo desde un panel intuitivo y fácil de usar.",
            
            "video1-demo/05-demo-usuario.mp3": "Los usuarios disfrutan de una experiencia excepcional. Pueden ver sus puntos acumulados de forma clara, explorar un catálogo atractivo de beneficios, canjear recompensas con un solo clic, ver su historial completo e invitar amigos para ganar puntos adicionales.",
            
            "video1-demo/06-caracteristicas.mp3": "Nuestras características técnicas garantizan la mejor experiencia. Incluimos seguridad de nivel bancario, diseño responsive para todos los dispositivos, integración fácil con sistemas existentes, soporte técnico 24/7 y escalabilidad para el crecimiento de tu negocio.",
            
            "video1-demo/07-beneficios.mp3": "Los beneficios para tu negocio son significativos. Nuestros clientes han experimentado un aumento del 67% en retención de clientes, incremento del ticket promedio, obtención de datos valiosos sobre el comportamiento de los clientes y diferenciación competitiva en el mercado.",
            
            "video1-demo/08-casos-exito.mp3": "Empresas de diversos sectores ya confían en nosotros. Un restaurante XYZ experimentó un aumento del 45% en ventas, una tienda ABC logró un 30% más de retención de clientes, y un servicio DEF alcanzó un 50% más de engagement con su programa.",
            
            "video1-demo/09-cta.mp3": "¿Listo para transformar tu negocio? Agenda una demo gratuita personalizada, visita puntosestilo.com para más información, llámanos al +57 555-555-555 o envíanos un email a info@puntosestilo.com. El futuro de la fidelización está aquí."
        }
        
        for filename, text in scripts.items():
            success = self.generate_audio(text, voice_id, filename)
            if not success:
                print(f"⚠️ Saltando {filename} debido a error")
            time.sleep(1)  # Pausa entre requests

    def generate_video2_audio(self, voice_id):
        """Generar audio para Video 2: Promocional"""
        print("\n🎬 Generando audio para Video 2: Promocional")
        
        script = "¿Quieres aumentar la lealtad de tus clientes? Los programas de fidelización tradicionales son complejos y costosos. Puntos Estilo simplifica todo. Sistema completo en una plataforma. Acumulación automática, catálogo personalizable, analytics en tiempo real. ¡Comienza hoy! puntosestilo.com"
        
        self.generate_audio(script, voice_id, "video2-promocional/promocional-completo.mp3")

    def generate_video3_audio(self, voice_id):
        """Generar audio para Video 3: Tutorial"""
        print("\n🎬 Generando audio para Video 3: Tutorial")
        
        scripts = {
            "video3-tutorial/01-intro.mp3": "En este tutorial aprenderás a usar Puntos Estilo paso a paso. Te guiaremos a través de todas las funcionalidades para que aproveches al máximo nuestra plataforma.",
            
            "video3-tutorial/02-registro.mp3": "Primero, ve a puntosestilo.com. Haz clic en el botón 'Registrarse' en la esquina superior derecha. Completa el formulario con tus datos personales, verifica tu dirección de email y luego inicia sesión con tus credenciales.",
            
            "video3-tutorial/03-panel.mp3": "En tu panel de usuario verás toda la información importante. En la parte superior encontrarás tus puntos acumulados, en el centro el historial de transacciones, a la izquierda los beneficios disponibles y en la esquina superior derecha la configuración de tu perfil.",
            
            "video3-tutorial/04-catalogo.mp3": "Para explorar beneficios, navega por las diferentes categorías disponibles. Puedes filtrar por cantidad de puntos, leer las descripciones detalladas de cada beneficio y ver las imágenes de alta calidad. Todo está organizado de manera intuitiva.",
            
            "video3-tutorial/05-canje.mp3": "Para canjear un beneficio, simplemente selecciónalo del catálogo, confirma la cantidad de puntos que se descontarán, recibe tu código único de canje y úsalo en el establecimiento correspondiente. El proceso es rápido y seguro.",
            
            "video3-tutorial/06-referidos.mp3": "Invita amigos y gana puntos adicionales. Comparte tu enlace personal de invitación, gana puntos por cada registro exitoso, rastrea tus invitaciones en tiempo real y recibe recompensas por tu red de referidos.",
            
            "video3-tutorial/07-configuracion.mp3": "Personaliza tu experiencia actualizando tu perfil con información actualizada, configurando las notificaciones según tus preferencias, gestionando tu privacidad y conectando tus redes sociales para mayor integración.",
            
            "video3-tutorial/08-soporte.mp3": "Si necesitas ayuda en cualquier momento, tenemos múltiples canales de soporte. Chat en vivo disponible 24/7, email de soporte, teléfono directo y una sección de FAQ completa en nuestra web."
        }
        
        for filename, text in scripts.items():
            success = self.generate_audio(text, voice_id, filename)
            if not success:
                print(f"⚠️ Saltando {filename} debido a error")
            time.sleep(1)  # Pausa entre requests

    def generate_all_audio(self, voice_id):
        """Generar todo el audio para los 3 videos"""
        print("🎤 Iniciando generación completa de audio...")
        
        self.generate_video1_audio(voice_id)
        self.generate_video2_audio(voice_id)
        self.generate_video3_audio(voice_id)
        
        print("\n🎉 ¡Generación de audio completada!")
        print("📁 Archivos guardados en la carpeta 'audio/'")

def main():
    print("🎤 Generador Automático de Audio - Puntos Estilo")
    print("=" * 50)
    
    # Solicitar API key
    api_key = input("🔑 Ingresa tu API key de ElevenLabs: ").strip()
    
    if not api_key:
        print("❌ API key requerida. Obtén una en https://elevenlabs.io/")
        return
    
    # Crear generador
    generator = AudioGenerator(api_key)
    
    # Mostrar voces disponibles
    voices = generator.get_voices()
    
    if not voices:
        print("❌ No se pudieron obtener las voces. Verifica tu API key.")
        return
    
    # Seleccionar voz
    print("\n🎯 Selecciona una voz:")
    print("1. Carlos (Masculino profesional)")
    print("2. Ana (Femenino energético)")
    print("3. Ver todas las voces")
    print("4. Ingresar ID de voz manualmente")
    
    choice = input("\nSelecciona una opción (1-4): ").strip()
    
    voice_id = None
    
    if choice == "1":
        # Buscar voz masculina
        for voice in voices:
            if "carlos" in voice["name"].lower() or "male" in voice["name"].lower():
                voice_id = voice["voice_id"]
                print(f"✅ Voz seleccionada: {voice['name']}")
                break
    elif choice == "2":
        # Buscar voz femenina
        for voice in voices:
            if "ana" in voice["name"].lower() or "female" in voice["name"].lower():
                voice_id = voice["voice_id"]
                print(f"✅ Voz seleccionada: {voice['name']}")
                break
    elif choice == "3":
        # Mostrar todas las voces
        for i, voice in enumerate(voices, 1):
            print(f"{i}. {voice['name']} (ID: {voice['voice_id']})")
        voice_choice = input("Selecciona el número de la voz: ").strip()
        try:
            voice_id = voices[int(voice_choice) - 1]["voice_id"]
        except (ValueError, IndexError):
            print("❌ Selección inválida")
            return
    elif choice == "4":
        voice_id = input("Ingresa el ID de la voz: ").strip()
    else:
        print("❌ Opción inválida")
        return
    
    if not voice_id:
        print("❌ No se pudo seleccionar una voz")
        return
    
    # Generar audio
    print(f"\n🎵 Generando audio con voz ID: {voice_id}")
    generator.generate_all_audio(voice_id)
    
    print("\n📋 Próximos pasos:")
    print("1. Revisar los archivos de audio generados")
    print("2. Importar en DaVinci Resolve")
    print("3. Sincronizar con grabación de pantalla")
    print("4. Agregar música de fondo")
    print("5. Exportar video final")

if __name__ == "__main__":
    main() 