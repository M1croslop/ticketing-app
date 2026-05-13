# 🔍 Revisión Técnica — Synapso Ticketing App (Parte 2)
> Vistas · Migraciones · Seeders · Rutas

---

## 📁 VISTAS

### `tickets/index.blade.php`

**PROBLEMA 24**

- **ARCHIVO:** `index.blade.php`
- **LÍNEA:** 81–83
- **SEVERIDAD:** 🟡 ADVERTENCIA
- **PROBLEMA:** Ternario anidado en la clase Tailwind no maneja el caso `'urgent'`. Un ticket urgente cae al estilo `low`.
- **CÓDIGO ACTUAL:**
```blade
{{ $ticket->priority == 'high' ? 'bg-synapso-priority-high-bg ...' :
   ($ticket->priority == 'medium' ? 'bg-synapso-priority-mid-bg ...' :
       'bg-synapso-priority-low-bg ...') }}
```
- **CÓDIGO CORREGIDO:** Usar un array de mapeo en `@php` o un componente Blade:
```blade
@php
$priorityClasses = [
    'urgent' => 'bg-synapso-priority-urgent-bg text-synapso-priority-urgent-text',
    'high'   => 'bg-synapso-priority-high-bg text-synapso-priority-high-text',
    'medium' => 'bg-synapso-priority-mid-bg text-synapso-priority-mid-text',
    'low'    => 'bg-synapso-priority-low-bg text-synapso-priority-low-text',
];
@endphp
<span class="px-2 py-1 text-xs font-semibold rounded {{ $priorityClasses[$ticket->priority] ?? '' }}">
```
- **RAZÓN:** El ternario no es escalable y omite `urgent`. El mismo patrón de mapeo ya se usa correctamente en `show.blade.php`.

---

**PROBLEMA 25**

- **ARCHIVO:** `index.blade.php`
- **LÍNEA:** 95–97
- **SEVERIDAD:** 🟢 SUGERENCIA
- **PROBLEMA:** El badge de estado tampoco maneja `'resolved'` explícitamente — cae al estilo `open`. Solo distingue `in_progress` y `closed/done`.
- **CÓDIGO CORREGIDO:** Usar el mismo patrón de array:
```blade
@php
$statusClasses = [
    'open'        => 'bg-synapso-status-open-bg text-synapso-status-open-text',
    'in_progress' => 'bg-synapso-status-progress-bg text-synapso-status-progress-text',
    'resolved'    => 'bg-synapso-status-done-bg text-synapso-status-done-text',
    'closed'      => 'bg-synapso-status-done-bg text-synapso-status-done-text',
];
@endphp
```

---

**PROBLEMA 26**

- **ARCHIVO:** `index.blade.php`
- **LÍNEA:** 117 / 131–144
- **SEVERIDAD:** 🟡 ADVERTENCIA
- **PROBLEMA:** El check de permisos en la vista (`auth()->id() === $ticket->user_id || role === 'admin'`) está duplicado: ya existe en el controlador `destroy()`. Si la lógica cambia, hay que actualizarla en dos lugares.
- **RAZÓN:** Las vistas no deberían contener lógica de autorización compleja. Debería usarse `@can('delete', $ticket)` apoyado en la Policy.
- **CÓDIGO CORREGIDO:**
```blade
@can('delete', $ticket)
    {{-- Botones editar y eliminar --}}
@endcan
```

---

**PROBLEMA 27**

- **ARCHIVO:** `index.blade.php`
- **LÍNEA:** 180
- **SEVERIDAD:** 🟡 ADVERTENCIA
- **PROBLEMA:** La URL del fetch JS se construye con Blade `'{{ route('tickets.index') }}'` dentro de un script. Aunque el CSRF no aplica a GET, el endpoint devuelve HTML completo, no JSON. Si el layout cambia, el selector `#tickets-tbody` puede fallar silenciosamente.
- **RAZÓN:** El enfoque es funcional pero frágil. Se recomienda a futuro un endpoint JSON dedicado o usar Livewire/Inertia. Para ahora, agregar manejo de error más explícito.

---

### `tickets/show.blade.php`

**PROBLEMA 28**

- **ARCHIVO:** `show.blade.php`
- **LÍNEA:** 52–57
- **SEVERIDAD:** 🟡 ADVERTENCIA
- **PROBLEMA:** Bloque `@php` con lógica de negocio en la vista. La determinación de `$canEdit`, `$isAdmin`, `$isAgent` es lógica de autorización que no pertenece a la vista.
- **CÓDIGO ACTUAL:**
```blade
@php
    $user     = Auth::user();
    $isAdmin  = $user->role === 'admin';
    $isAgent  = $user->role === 'agent' && $ticket->agent_id === $user->id;
    $canEdit  = $isAdmin || $isAgent;
@endphp
```
- **CÓDIGO CORREGIDO:** Mover a la Policy o al controlador como variable:
```php
// En TicketController::show()
$canEdit = $ticket->canBeEditedBy(Auth::user()); // método en el modelo
return view('tickets.show', compact('ticket', 'agents', 'canEdit'));
```
```php
// En Ticket.php
public function canBeEditedBy(User $user): bool
{
    return $user->role === 'admin'
        || ($user->role === 'agent' && $this->agent_id === $user->id);
}
```

---

**PROBLEMA 29**

- **ARCHIVO:** `show.blade.php`
- **LÍNEA:** 130
- **SEVERIDAD:** 🟡 ADVERTENCIA
- **PROBLEMA:** `$ticket->statusLogs->sortByDesc('created_at')` — el model `TicketStatusLog` tiene `$timestamps = false`, entonces **no existe la columna `created_at`**. El sort no funciona o lanza un error.
- **CÓDIGO ACTUAL:**
```blade
@foreach($ticket->statusLogs->sortByDesc('created_at') as $log)
    {{ $log->created_at->format('d/m/Y h:i A') }}
```
- **CÓDIGO CORREGIDO:** Usar `changed_at` (el campo que sí existe):
```blade
@foreach($ticket->statusLogs->sortByDesc('changed_at') as $log)
    {{ $log->changed_at->format('d/m/Y h:i A') }}
```
Y castear en el modelo:
```php
protected $casts = ['changed_at' => 'datetime'];
```

---

**PROBLEMA 30**

- **ARCHIVO:** `show.blade.php`
- **LÍNEA:** 63–73
- **SEVERIDAD:** 🟡 ADVERTENCIA
- **PROBLEMA:** El form de cambio de estado hace submit automático con `onchange="this.form.submit()"`, pero no tiene confirmación. Un clic accidental en el select cierra o resuelve un ticket irreversiblemente.
- **CÓDIGO CORREGIDO:**
```blade
onchange="if(confirm('¿Confirmas el cambio de estado?')) this.form.submit();"
```

---

### `tickets/create.blade.php`

**PROBLEMA 31**

- **ARCHIVO:** `create.blade.php`
- **LÍNEA:** 106–108
- **SEVERIDAD:** 🟡 ADVERTENCIA
- **PROBLEMA:** El campo `due_date` es editable por cualquier usuario, incluidos clientes. Un cliente podría establecer manualmente una fecha límite muy lejana, evadiendo la lógica SLA.
- **CÓDIGO CORREGIDO:**
```blade
@if(auth()->user()->role === 'admin' || auth()->user()->role === 'agent')
    <div>
        <label ...>Fecha límite</label>
        <input type="date" name="due_date" ...>
    </div>
@endif
```

---

**PROBLEMA 32**

- **ARCHIVO:** `create.blade.php`
- **LÍNEA:** 62–74
- **SEVERIDAD:** 🟡 ADVERTENCIA
- **PROBLEMA:** El select de `agent_id` es visible para TODOS los usuarios, incluidos clientes. Un cliente no debería poder asignarse un agente.
- **CÓDIGO CORREGIDO:**
```blade
@if(auth()->user()->role !== 'client')
    {{-- Select de agente --}}
@endif
```

---

### `tickets/edit.blade.php`

**PROBLEMA 33**

- **ARCHIVO:** `edit.blade.php`
- **LÍNEA:** 84–88
- **SEVERIDAD:** 🟡 ADVERTENCIA
- **PROBLEMA:** `@selected` usa `==` (comparación laxa PHP) en `create.blade.php` para `category_id`, pero `edit.blade.php` usa la sintaxis de atributo HTML `{{ $ticket->category_id == $category->id ? 'selected' : '' }}` — inconsistencia de estilo. Además no usa `old()` para el fallback.
- **CÓDIGO ACTUAL:**
```blade
<option value="{{ $category->id }}"
    {{ $ticket->category_id == $category->id ? 'selected' : '' }}>
```
- **CÓDIGO CORREGIDO:**
```blade
<option value="{{ $category->id }}"
    @selected(old('category_id', $ticket->category_id) == $category->id)>
```

---

**PROBLEMA 34**

- **ARCHIVO:** `edit.blade.php`
- **LÍNEA:** 29–40
- **SEVERIDAD:** 🟡 ADVERTENCIA
- **PROBLEMA:** Faltan los `@error` blocks para `title` y `description`. Si la validación falla, el usuario no ve mensajes de error en esos campos.
- **CÓDIGO CORREGIDO:**
```blade
<input type="text" name="title" value="{{ old('title', $ticket->title) }}" ...>
@error('title')
    <p class="text-synapso-danger text-xs mt-1">{{ $message }}</p>
@enderror
```

---

### `dashboard.blade.php`

**PROBLEMA 35**

- **ARCHIVO:** `dashboard.blade.php`
- **LÍNEA:** 169
- **SEVERIDAD:** 🟢 SUGERENCIA
- **PROBLEMA:** HTML mal indentado / estructura incorrecta. El `</div>` de cierre del flex kanban (línea 167) está dentro de `</main>`, pero hay un `</div>` extra en la línea 169 que no corresponde a ningún elemento abierto.
- **RAZÓN:** Dificulta el mantenimiento y puede causar layout quebrado en ciertos navegadores.

---

**PROBLEMA 36**

- **ARCHIVO:** `dashboard.blade.php`
- **LÍNEA:** 11
- **SEVERIDAD:** 🟡 ADVERTENCIA
- **PROBLEMA:** El dashboard muestra estadísticas globales a todos los usuarios. Un cliente ve el total de tickets abiertos de toda la organización, lo que puede ser información sensible.
- **CÓDIGO CORREGIDO:** Filtrar métricas según el rol del usuario autenticado (lógica en `DashboardController`).

---

### `components/alert.blade.php`

**PROBLEMA 37**

- **ARCHIVO:** `alert.blade.php`
- **LÍNEA:** 103
- **SEVERIDAD:** 🟡 ADVERTENCIA
- **PROBLEMA:** Se usa `{!! $alert['icon'] !!}` (HTML sin escapar) para renderizar el SVG del ícono. Los valores de `icon` están definidos en el propio componente PHP, no vienen del usuario, así que el riesgo XSS es mínimo. Sin embargo, si algún día este componente se extiende para aceptar íconos externos, sería una vulnerabilidad.
- **RAZÓN:** Documentar el riesgo con un comentario y mantener el array de íconos siempre en el propio componente.

---

### `layouts/app.blade.php`

**PROBLEMA 38**

- **ARCHIVO:** `app.blade.php`
- **LÍNEA:** 11
- **SEVERIDAD:** 🟡 ADVERTENCIA
- **PROBLEMA:** Alpine.js se carga desde CDN sin Subresource Integrity (SRI). Si el CDN es comprometido, se ejecuta JavaScript malicioso en todos los usuarios.
- **CÓDIGO ACTUAL:**
```html
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
```
- **CÓDIGO CORREGIDO:** Instalar Alpine vía npm (ya está en `package.json`) y quitarlo del CDN, O agregar SRI:
```html
<script defer
    src="https://unpkg.com/alpinejs@3.14.1/dist/cdn.min.js"
    integrity="sha384-[hash]"
    crossorigin="anonymous"></script>
```
- **RAZÓN:** Seguridad de supply chain. La versión `@3.x.x` también puede cargar versiones distintas en diferentes momentos.

---

## 📁 RUTAS

### `routes/web.php`

**PROBLEMA 39**

- **ARCHIVO:** `web.php`
- **LÍNEA:** 23–30
- **SEVERIDAD:** 🟡 ADVERTENCIA
- **PROBLEMA:** `require __DIR__ . '/auth.php'` está en medio del archivo, entre la definición de rutas de tickets y las de comentarios. Esto dificulta la lectura.
- **CÓDIGO CORREGIDO:** Mover el `require` al final del archivo, después de todas las rutas personalizadas.

---

**PROBLEMA 40**

- **ARCHIVO:** `web.php`
- **LÍNEA:** 13–15
- **SEVERIDAD:** 🟢 SUGERENCIA
- **PROBLEMA:** La ruta `/dashboard` requiere `verified` (email verificado) pero las rutas de tickets no. Un usuario sin email verificado puede crear tickets pero no ver el dashboard — inconsistencia.
- **CÓDIGO CORREGIDO:** Decidir una política consistente: o todos requieren `verified` o ninguno. Para un sistema IT interno, probablemente ninguno necesite verificación.

---

## 📁 MIGRACIONES

### `create_tickets_table.php`

**PROBLEMA 41**

- **ARCHIVO:** `create_tickets_table.php`
- **LÍNEA:** 20–21
- **SEVERIDAD:** 🟡 ADVERTENCIA
- **PROBLEMA:** `status` y `priority` son `string` libre, sin restricción de valores en DB. Aunque se valida en el FormRequest, si alguien inserta directamente en DB o por un bug en el código, puede quedar un valor inválido.
- **CÓDIGO CORREGIDO:** Usar `enum` en la migración:
```php
$table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
$table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
```
- **RAZÓN:** Defense in depth — la DB es la última línea de defensa para la integridad de datos.

---

## 📁 SEEDERS Y FACTORIES

### `TicketFactory.php`

**PROBLEMA 42**

- **ARCHIVO:** `TicketFactory.php`
- **LÍNEA:** 22–24
- **SEVERIDAD:** 🟡 ADVERTENCIA
- **PROBLEMA:** `User::inRandomOrder()->first()` se llama dos veces (para `user` y `agent`) sin filtrar por rol. El factory puede asignar un agente con rol `client` a un ticket.
- **CÓDIGO CORREGIDO:**
```php
$user  = User::where('role', 'client')->inRandomOrder()->first()
    ?? User::factory()->create(['role' => 'client']);
$agent = User::whereIn('role', ['agent', 'admin'])->inRandomOrder()->first()
    ?? User::factory()->create(['role' => 'agent']);
```

---

**PROBLEMA 43**

- **ARCHIVO:** `TicketFactory.php`
- **LÍNEA:** 29
- **SEVERIDAD:** 🟢 SUGERENCIA
- **PROBLEMA:** El factory no incluye `'urgent'` en las prioridades ni `'resolved'` en los estados, dando una distribución de datos de prueba incompleta.
- **CÓDIGO CORREGIDO:**
```php
'status'   => fake()->randomElement(Ticket::STATUSES),
'priority' => fake()->randomElement(Ticket::PRIORITIES),
```

---

## 📋 RESUMEN EJECUTIVO

### Conteo por severidad

| Severidad | Cantidad |
|-----------|----------|
| 🔴 CRÍTICO | **7** |
| 🟡 ADVERTENCIA | **26** |
| 🟢 SUGERENCIA | **10** |
| **TOTAL** | **43** |

---

### 🔺 TOP 3 — Problemas a resolver PRIMERO

**#1 — TicketPolicy completamente vacía (Problema 20 + 13)**
> Todos los métodos retornan `false`. La autorización del sistema depende de checks manuales inconsistentes. Implementar la Policy y usar `$this->authorize()` en todos los métodos del `TicketController`.

**#2 — CommentController::destroy() sin autorización (Problema 16)**
> Cualquier usuario autenticado puede borrar cualquier comentario del sistema enviando `DELETE /tickets/{ticket}/comments/{comment}`. Es la vulnerabilidad más explotable del proyecto.

**#3 — DashboardController carga todos los tickets en memoria (Problema 17)**
> `->get()->avg(...)` en PHP en lugar de `AVG()` en SQL. Con 10,000 tickets resueltos, esto causa un timeout o crash de memoria. Reemplazar con una query SQL agregada.

---

### 📊 Calidad estimada por archivo

| Archivo | Calidad |
|---------|---------|
| `Ticket.php` | 80% — Buena estructura SLA, faltan guards en fillable |
| `User.php` | 65% — Import HasMany faltante (error crítico), role en fillable |
| `Comment.php` | 60% — SoftDeletes inconsistente, tipos sin declarar |
| `TicketStatusLog.php` | 70% — Funcional, casts faltantes, indentación |
| `TicketController.php` | 55% — Sin Policy, LIKE sin escapar, DRY violations |
| `CommentController.php` | 40% — Sin autorización en destroy, sin FormRequest |
| `DashboardController.php` | 50% — Query crítica en PHP, métricas no filtradas |
| `ProfileController.php` | 80% — Sólido, orden delete/logout invertido |
| `TicketPolicy.php` | 10% — Existe pero es completamente inoperativa |
| `StoreTicketRequest.php` | 75% — Buena validación, falta agent_id |
| `UpdateTicketRequest.php` | 72% — after_or_equal problemático |
| `tickets/index.blade.php` | 65% — Ternario incompleto, auth en vista |
| `tickets/show.blade.php` | 60% — @php con lógica, created_at inexistente en logs |
| `tickets/create.blade.php` | 65% — Campos sensibles para todos los roles |
| `tickets/edit.blade.php` | 60% — Falta @error en campos principales |
| `dashboard.blade.php` | 65% — HTML roto, métricas globales |
| `web.php` | 78% — Funcional, require mal ubicado |
| `Migraciones` | 70% — Falta enum en status/priority |
| `TicketFactory.php` | 65% — Roles mezclados, estados incompletos |

### **Calidad global del proyecto: ~64%**

> **Nota positiva para el equipo junior:** La arquitectura base es correcta — MVC bien separado, FormRequests en uso, Eloquent con relaciones bien definidas, SLA automático con `booted()`, diseño visual profesional con sistema de tokens Tailwind, y componente de alertas bien implementado. Los problemas encontrados son típicos de un primer proyecto universitario y completamente resolubles. Con los 3 fixes prioritarios, la calidad sube a ~78%.
