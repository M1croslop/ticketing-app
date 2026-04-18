# Ticketing It App

Aplicacion de gestión de servicios de Ticketing TI (ITSM).
Su objetivo principal es optimizar la experiencia de los empleados y mejorar la eficiencia de los equipos de tecnología mediante una plataforma unificada.

## Requisitos Previos

Asegúrate de tener instalados los siguientes programas en tu entorno local:

* PHP
* Composer
* Node.js
* MySQL (Gestor de base de datos local)

## Instalación

Ejecuta estos comandos en tu terminal para preparar el código fuente:

1. Descarga el repositorio:
```bash
   git clone https://github.com/M1croslop/ticketing-app.git
````

2.  Ingresa al directorio:

<!-- end list -->

```bash
   cd ticketing-app
```

3.  Instala los paquetes de backend y frontend:

<!-- end list -->

```bash
   composer install
   npm install
```

4.  Prepara las variables de entorno:

<!-- end list -->

```bash
   cp .env.example .env
```

5.  Genera la clave de seguridad:

<!-- end list -->

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
DB_DATABASE=ticketing_app
DB_USERNAME=tu_usuario_local
DB_PASSWORD=tu_contraseña_local
```

Construye las tablas y carga los datos de prueba con este comando:

```bash
php artisan migrate --seed
```

## Iniciar el Servidor

Necesitas dos terminales abiertas para ejecutar la aplicación correctamente.

En la primera terminal, levanta el servidor backend:

```bash
php artisan serve
```

En la segunda terminal, compila los recursos visuales:

```bash
npm run dev
```

## Diagrama de la Base de Datos

La estructura de datos del sistema sigue el diseño definido en el siguiente esquema. Consulta este diagrama antes de realizar nuevas migraciones en Laravel.

<img width="1080" height="1080" alt="schema" src="https://github.com/user-attachments/assets/cea6e711-2e6b-42ca-86bd-31f088b85324" />


## Reglas de Trabajo en Equipo

**1. Flujo de Ramas (GitFlow)**

* Todo el trabajo diario se realiza en la rama `develop`.
* Antes de iniciar a programar, ejecuta `git checkout develop` y `git pull origin develop`.
* La rama `main` está protegida. Solo recibe actualizaciones al final del sprint mediante un Pull Request.

**2. Estándar de Commits**

Utilizamos la especificación [Conventional Commits v1.0.0](https://www.conventionalcommits.org/en/v1.0.0/). Sigue esta estructura:
`tipo(alcance opcional): descripción corta`

* **feat**: Nueva funcionalidad para el usuario.
* **fix**: Solución de un error en el código.
* **docs**: Cambios exclusivamente en la documentación.
* **style**: Cambios de formato que no afectan la lógica.
* **refactor**: Cambio en el código que no corrige errores ni añade funciones.
* **test**: Añadir o corregir pruebas existentes.
* **chore**: Actualizaciones de tareas de construcción o herramientas locales.

> [\!IMPORTANT]
> *Ejemplo:* `feat(auth): implementar validación de correo electrónico`

## License

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>
The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
