# Ticketing It App

Aplicacion de gestión de servicios de Ticketing TI (ITSM).
Su objetivo principal es optimizar la experiencia de los empleados y mejorar la eficiencia de los equipos de tecnología mediante una plataforma unificada.

## Requisitos Previos

Asegúrate de tener instalados los siguientes programas en tu entorno local:

* PHP
* Composer
* Node.js
* Mysql - Gestor de db local

## Instalación

Ejecuta estos comandos en tu terminal para preparar el código fuente:

1. Descarga el repositorio:
```bash
   git clone https://github.com/M1croslop/ticketing-app.git
```

2. Ingresa al directorio:
```bash
   cd ticketing-app
```

3. Instala los paquetes de backend:
```bash
   composer install
```

4. Prepara las variables de entorno:
```bash
   cp .env.example .env
```

5. Genera la clave de seguridad:
```bash
   php artisan key:generate
```

## Configuración de la Base de Datos

Crea una base de datos nueva en tu gestor local. 

Abre el archivo `.env` en tu editor de código. Modifica la sección de base de datos con tus credenciales locales:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_de_la_base_de_datos
DB_USERNAME=tu_usuario_local
DB_PASSWORD=tu_contraseña_local
```

Construye las tablas y carga los datos de prueba con este comando:
```bash
php artisan migrate --seed
```

## Iniciar el Servidor

Ejecuta el servidor de desarrollo para procesar la interfaz visual:
```bash
php artisan serve
```

## Diagrama de la Base de Datos

La estructura de datos del sistema sigue el diseño definido en el siguiente esquema. Consulta este diagrama antes de realizar nuevas migraciones en Laravel.

<img width="1080" height="1080" alt="schema" src="https://github.com/user-attachments/assets/398dfe6f-f922-4ff5-a971-7c17de89bd50" />

> [!IMPORTANT]
> ### Reglas de Trabajo en Equipo
>
> Para mantener un historial de cambios profesional, utilizamos la especificación [Conventional Commits v1.0.0](https://www.conventionalcommits.org/en/v1.0.0/). Todo el equipo debe seguir esta estructura en sus mensajes:
>
> `tipo(alcance opcional): descripción corta`
>
> ## Tipos de Mensajes
> * **feat**: Nueva funcionalidad para el usuario.
> * **fix**: Solución de un error en el código.
> * **docs**: Cambios exclusivamente en la documentación.
> * **style**: Cambios de formato que no afectan la lógica.
> * **refactor**: Cambio en el código que no corrige errores ni añade funciones.
> * **test**: Añadir o corregir pruebas existentes.
> * **chore**: Actualizaciones de tareas de construcción o herramientas locales.
>
> ### Ejemplo Práctico
> `feat(auth): implementar validación de correo electrónico`

## License

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>
The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
