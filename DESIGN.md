# Synapso: Sistema de Identidad Visual

Este documento define las bases visuales de **Synapso**. El objetivo es mantener una interfaz coherente, profesional, minimalista y enfocada en la gestión eficiente de tickets para el departamento de IT.

## 1. Concepto de views
![alt text](image.png)
La interfaz debe priorizar:
- Lectura rápida de tickets
- Identificación inmediata de prioridades
- Flujo eficiente para agentes IT

## 2. Paleta de Colores
Utilizaremos estos códigos exactos para configurar nuestro archivo `tailwind.config.js`.

| Aplicación | Nombre | Hexadecimal | Color (Referencia) | uso |
| :--- | :--- | :--- | :--- |
| **Primario** | `primary-navy` | `#4338CA` | Azul claro purpleish |Navegación, botones principales|
| **Acento** | `accent-gold` | `#FFFFFF` | blanco |Textos sobre color primario |
| **Fondo** | `background-gray` | `#F8FAFC` | Gris muy claro | Fondo general |
| **Éxito** | `success-green`| `#059669` | Esmeralda | Estados positivos |
| **Alerta** | `alert-red`  | `#DC2626` | Rojo corporativo | Errores y prioridad crítica |

### Configuración en Tailwind/Bootstrap
```javascript
// tailwind.config.js
module.exports = {
  theme: {
    extend: {
      colors: {
        synapso: {
          navy: '#4338CA',
          gold: '#FFFFFF',
          bg: '#F8FAFC',
          success: '#059669',
          danger: '#DC2626'
        }
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif']
      }
    }
  }
}
```

## 3. Tipografía
Usaremos fuentes sans-serif para maximizar la legibilidad en pantallas de alta densidad.

* **Principal:** `Inter` (vía Google Fonts).
* **Alternativa:** `System UI Sans-Serif`.
* **Tamaño Base:** 16px para cuerpo de texto.
* **Títulos:** Peso `Font-Bold` (700) con color `synapso-navy`.

## 4. Componentes UI Reutilizables

### Botones (Buttons)
* **Primario:** Fondo `primary-navy`, texto blanco, bordes redondeados (`rounded-lg`).
* **Acción:** Fondo `accent-gold`, efecto hover con sombra ligera.
* **Éxito:** Fondo success-green
* **Peligro:** Fondo alert-red
* **Secundario:** Borde gris, fondo transparente

### Etiquetas de Estado (Status Badges)
Para la tabla de tickets, usaremos estos estilos:
* **Open:** Fondo azul claro, texto azul oscuro.
* **In Progress:** Fondo ámbar claro, texto ámbar oscuro.
* **Resolved:** Fondo verde claro, texto verde oscuro.


## 5. Iconografía
Utilizaremos exclusivamente (**Heroicons**)[https://heroicons.com/] (en su versión Outline para navegación y Solid para acciones críticas), o por su defecto (**Carbon**)[https://carbondesignsystem.com/elements/icons/library/]. Esto mantiene el peso visual ligero.

## 6. Layout y Espaciado
* **Contenedor Principal:** Máximo `max-w-7xl`.
* **Padding estándar:** `p-4` en móviles, `p-8` en escritorio.
* **Tarjetas (Cards):** Fondo blanco, borde gris muy fino (`border-slate-200`), sin sombras pesadas.

## 7.Navegación
Navegación

Estructura principal de navegación del sistema.

* **Sidebar:**

  * **Dashboard**
  * **Tickets**
  * **Mis Tickets**
  * **Reportes**

* **Dashboard:**

  * **Tickets CREADOS**
  * **Tickets EN PROGRESO**
  * **Tickets RESUELTOS**

## 8. Feedback del Sistema

Elementos de interacción y respuesta del sistema.

* **Toasts:**

  * **Ticket creado**
  * **Ticket asignado**
  * **Error al guardar**

* **Estados de carga:**

  * **Spinner**
  * **Skeletons**

## 11. Reglas de Consistencia

**Lineamientos obligatorios para mantener uniformidad.**

* **No usar colores fuera de la paleta**
* **Máximo 2–3 colores por componente**
* **No usar sombras fuertes**
* **Mantener diseño limpio y funcional**

## 12. Flujo del Sistema de Tickets

Estados definidos para el ciclo de vida de los tickets.

* **Open**
* **In Progress**
* **Resolved**

---

> [!TIP]
> **Regla de Oro para el trabajo en equipo:** Si un elemento no está definido en este documento, consulta con el encargado de diseño antes de implementarlo. No uses colores aleatorios fuera de la paleta oficial.