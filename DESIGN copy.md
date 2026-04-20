````text id="k9x2lm"
# Synapso: Sistema de Identidad Visual

Este documento define las bases visuales de **Synapso**. El objetivo es mantener una interfaz coherente, profesional, minimalista y enfocada en la gestión eficiente de tickets para el departamento de IT.

## 1. Concepto de views
[png-de-mockups]
La interfaz debe priorizar:
- Lectura rápida de tickets
- Identificación inmediata de prioridades
- Flujo eficiente para agentes IT

## 2. Paleta de Colores
Utilizaremos estos códigos exactos para configurar nuestro archivo `tailwind.config.js`.

| Aplicación | Nombre | Hexadecimal | Color (Referencia) | uso |
| :--- | :--- | :--- | :--- | :--- |
| **Primario** | `primary-navy` | `#1E293B` | Azul Oscuro (Deep Slate) | Navegación, botones principales |
| **Acento** | `accent-gold` | `#D97706` | Ámbar / Oro suave | Acciones importantes |
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
          navy: '#1E293B',
          gold: '#D97706',
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
````

## 3. Tipografía

Usaremos fuentes sans-serif para maximizar la legibilidad en pantallas de alta densidad.

* Principal: Inter (Google Fonts)
* Alternativa: System UI Sans-Serif
* Tamaño base: 16px
* Títulos: Font-Bold (700) con color synapso-navy

## 4. Componentes UI Reutilizables
* Botones:

  * Primario: Fondo primary-navy, texto blanco, bordes rounded-lg
  * Acción: Fondo accent-gold
  * Éxito: Fondo success-green
  * Peligro: Fondo alert-red
  * Secundario: Borde gris, fondo transparente

* Etiquetas de estado (Status Badges):

  * Open: Fondo azul claro, texto azul oscuro
  * In Progress: Fondo ámbar claro, texto ámbar oscuro
  * Resolved: Fondo verde claro, texto verde oscuro

## 5. Sistema de Prioridad

Se utilizarán los siguientes niveles para clasificar los tickets.

* Baja: color gris
* Media: color azul
* Alta: color accent-gold
* Crítica: color alert-red

## 6. Tabla de Tickets

La tabla principal debe incluir la siguiente información:

* ID del ticket
* Usuario
* Problema
* Estado
* Prioridad
* Fecha
* Agente asignado

Debe permitir:

* Filtros por estado
* Filtros por prioridad
* Búsqueda rápida

## 7. Iconografía

Se utilizarán iconos consistentes para mantener un diseño limpio.

* Heroicons (Outline para navegación, Solid para acciones)
* Alternativa: Carbon Icons

## 8. Layout y Navegación

Estructura principal de navegación del sistema.

* Sidebar:

  * Dashboard
  * Tickets
  * Mis Tickets
  * Reportes

* Dashboard:

  * Tickets abiertos
  * Tickets críticos
  * Tickets resueltos hoy

## 9. Layout y Espaciado

Reglas de estructura visual y distribución.

* Contenedor principal: max-w-7xl

* Padding:

  * Mobile: p-4
  * Desktop: p-8

* Cards:

  * Fondo blanco
  * Borde border-slate-200
  * Sin sombras pesadas

## 10. Feedback del Sistema

Elementos de interacción y respuesta del sistema.

* Toasts:

  * Ticket creado
  * Ticket asignado
  * Error al guardar

* Estados de carga:

  * Spinner
  * Skeletons

## 11. Reglas de Consistencia

Lineamientos obligatorios para mantener uniformidad.

* No usar colores fuera de la paleta
* Máximo 2–3 colores por componente
* No usar sombras fuertes
* Mantener diseño limpio y funcional

## 12. Flujo del Sistema de Tickets

Estados definidos para el ciclo de vida de los tickets.

* Open
* In Progress
* Resolved

---

> [!TIP]
> Regla de Oro para el trabajo en equipo: Si un elemento no está definido en este documento, consultar con el encargado de diseño antes de implementarlo. No usar colores aleatorios fuera de la paleta oficial.

```
```
