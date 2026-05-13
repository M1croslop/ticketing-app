# 🔍 Revisión Técnica — Synapso Ticketing App (Parte 1)
> Stack: Laravel 11 · MySQL · Blade · Tailwind CSS

---

## 📁 MODELOS

---

### `app/Models/User.php`

---

**PROBLEMA 1**

- **ARCHIVO:** `User.php`
- **LÍNEA:** 35
- **SEVERIDAD:** 🔴 CRÍTICO
- **PROBLEMA:** Import `HasMany` faltante — el método `tickets()` usa el tipo `HasMany` en su firma, pero el `use` correspondiente nunca se importa.
- **CÓDIGO ACTUAL:**
```php
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

public function tickets(): HasMany { ... }
```
- **CÓDIGO CORREGIDO:**
```php
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
```
- **RAZÓN:** Sin el `use`, PHP lanza `TypeError` en tiempo de ejecución al resolver el tipo de retorno. El modelo falla en producción.

---

**PROBLEMA 2**

- **ARCHIVO:** `User.php`
- **LÍNEA:** 4 (`roleLabels` en navbar)
- **SEVERIDAD:** 🟡 ADVERTENCIA
- **PROBLEMA:** El rol `'client'` en DB se llama `'client'`, pero en `navbar.blade.php` la clave del array `$roleLabels` es `'user'`. El badge de rol nunca coincide para clientes.
- **CÓDIGO ACTUAL (navbar.blade.php línea 4):**
```php
$roleLabels = ['admin' => 'Admin', 'agent' => 'Agente', 'user' => 'Usuario'];
```
- **CÓDIGO CORREGIDO:**
```php
$roleLabels = ['admin' => 'Admin', 'agent' => 'Agente', 'client' => 'Cliente'];
```
- **RAZÓN:** La migración define `enum('role', ['admin', 'agent', 'client'])`. El mapeo erróneo hace que los clientes siempre caigan al fallback `ucfirst($user->role)`, que muestra `"Client"` en inglés.

---

**PROBLEMA 3**

- **ARCHIVO:** `User.php`
- **LÍNEA:** 19
- **SEVERIDAD:** 🟡 ADVERTENCIA
- **PROBLEMA:** `'role'` está en `$fillable`, lo que permite mass-assignment del rol desde cualquier formulario que pase `role` en el request.
- **CÓDIGO ACTUAL:**
```php
protected $fillable = ['name', 'email', 'password', 'role'];
```
- **CÓDIGO CORREGIDO:**
```php
protected $fillable = ['name', 'email', 'password'];
```
- **RAZÓN:** Un usuario malintencionado podría registrarse como `admin` enviando `role=admin`. El rol debe asignarse exclusivamente en código controlado (seeders, métodos dedicados).

---

### `app/Models/Ticket.php`

**PROBLEMA 4**

- **ARCHIVO:** `Ticket.php`
- **LÍNEA:** 83–93
- **SEVERIDAD:** 🟡 ADVERTENCIA
- **PROBLEMA:** `'user_id'` y `'resolved_at'` están en `$fillable`. `user_id` se sobreescribe manualmente en el controlador, pero si alguien pasa `user_id` en el form request, podría crear tickets a nombre de otro usuario. `resolved_at` tampoco debería editarse libremente.
- **CÓDIGO ACTUAL:**
```php
protected $fillable = [
    'title', 'description', 'status', 'priority',
    'user_id', 'agent_id', 'category_id',
    'due_date', 'resolved_at',
];
```
- **CÓDIGO CORREGIDO:**
```php
protected $fillable = [
    'title', 'description', 'status', 'priority',
    'agent_id', 'category_id', 'due_date',
];
```
- **RAZÓN:** `user_id` debe asignarse explícitamente: `$ticket->user_id = Auth::id()`. `resolved_at` lo gestiona el observer `booted()`, no debería ser asignable masivamente.

---

**PROBLEMA 5**

- **ARCHIVO:** `Ticket.php`
- **LÍNEA:** 59–66
- **SEVERIDAD:** 🟢 SUGERENCIA
- **PROBLEMA:** El hook `updating` registra `resolved_at`, pero no hay un Observer ni evento que genere el `TicketStatusLog` cuando cambia el status. La tabla `ticket_status_logs` existe pero nunca se escribe desde código.
- **CÓDIGO ACTUAL:** *(no existe escritura a `TicketStatusLog` en ningún archivo)*
- **CÓDIGO CORREGIDO:** Agregar en el `booted()`:
```php
static::updated(function (Ticket $ticket): void {
    if ($ticket->wasChanged('status')) {
        TicketStatusLog::create([
            'ticket_id'  => $ticket->id,
            'changed_by' => Auth::id(),
            'old_status' => $ticket->getOriginal('status'),
            'new_status' => $ticket->status,
            'changed_at' => now(),
        ]);
    }
});
```
- **RAZÓN:** La vista `show.blade.php` renderiza el historial de estados (`statusLogs`), pero la colección siempre estará vacía porque nunca se insertan registros.

---

### `app/Models/Comment.php`

**PROBLEMA 6**

- **ARCHIVO:** `Comment.php`
- **LÍNEA:** 18–26
- **SEVERIDAD:** 🟢 SUGERENCIA
- **PROBLEMA:** Las relaciones no declaran tipo de retorno explícito. No es un error, pero rompe la consistencia con `Ticket.php` y dificulta el autocompletado del IDE.
- **CÓDIGO ACTUAL:**
```php
public function user()
{
    return $this->belongsTo(User::class);
}
```
- **CÓDIGO CORREGIDO:**
```php
use Illuminate\Database\Eloquent\Relations\BelongsTo;

public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}
```
- **RAZÓN:** Consistencia y mejor soporte de herramientas estáticas (PHPStan, IDE).

---

**PROBLEMA 7**

- **ARCHIVO:** `Comment.php` / migración `create_comments_table.php`
- **LÍNEA:** 24 (migración) / 10 (modelo)
- **SEVERIDAD:** 🟡 ADVERTENCIA
- **PROBLEMA:** La migración define `softDeletes()` para comments, pero el modelo `Comment` no usa el trait `SoftDeletes`. Esto deja `deleted_at` siempre en `NULL` y el borrado es físico (hard delete).
- **CÓDIGO ACTUAL:**
```php
class Comment extends Model
{
    use HasFactory; // SoftDeletes FALTA
```
- **CÓDIGO CORREGIDO:**
```php
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;
```
- **RAZÓN:** La columna `deleted_at` existe en la tabla pero Eloquent la ignora sin el trait, haciendo que `$comment->delete()` sea permanente.

---

### `app/Models/TicketStatusLog.php`

**PROBLEMA 8**

- **ARCHIVO:** `TicketStatusLog.php`
- **LÍNEA:** 11
- **SEVERIDAD:** 🟢 SUGERENCIA
- **PROBLEMA:** Indentación inconsistente: la apertura de `$fillable` tiene 3 espacios en lugar de 4.
- **CÓDIGO ACTUAL:**
```php
   protected $fillable = [
```
- **CÓDIGO CORREGIDO:**
```php
    protected $fillable = [
```

---

**PROBLEMA 9**

- **ARCHIVO:** `TicketStatusLog.php`
- **LÍNEA:** 16 / migración línea 25–26
- **SEVERIDAD:** 🟡 ADVERTENCIA
- **PROBLEMA:** `changed_at` está en `$fillable` y la migración la define con `useCurrent()`. El campo no está declarado en `$casts`, por lo que se trata como string. Además `$timestamps = false` es correcto, pero `changed_at` debería castearse a `datetime`.
- **CÓDIGO CORREGIDO:**
```php
protected $casts = [
    'changed_at' => 'datetime',
];
```

---

## 📁 CONTROLADORES

---

### `TicketController.php`

**PROBLEMA 10**

- **ARCHIVO:** `TicketController.php`
- **LÍNEA:** 24
- **SEVERIDAD:** 🔴 CRÍTICO
- **PROBLEMA:** La búsqueda LIKE usa interpolación directa de cadena en la query. Aunque Laravel bindea los parámetros correctamente via PDO, la interpolación `"%$search%"` es un antipatrón de legibilidad; además no hay sanitización del input para caracteres especiales de LIKE (`%`, `_`).
- **CÓDIGO ACTUAL:**
```php
->when(request('search'), fn($q, $search) => $q->where('title', 'like', "%$search%"))
```
- **CÓDIGO CORREGIDO:**
```php
->when(request('search'), function ($q, $search) {
    $safe = str_replace(['%', '_'], ['\%', '\_'], $search);
    $q->where('title', 'like', "%{$safe}%");
})
```
- **RAZÓN:** Sin escapar `%` y `_`, un usuario puede buscar `%` y obtener todos los tickets, o usar `_` para hacer búsquedas comodín no intencionadas.

---

**PROBLEMA 11**

- **ARCHIVO:** `TicketController.php`
- **LÍNEA:** 38 / 69 / 79
- **SEVERIDAD:** 🟡 ADVERTENCIA
- **PROBLEMA:** La query `User::where('role', 'agent')->orWhere('role', 'admin')->get()` se repite en `create()`, `show()` y `edit()` — 3 veces. Violación del principio DRY.
- **CÓDIGO ACTUAL:**
```php
$agents = User::where('role', 'agent')->orWhere('role', 'admin')->get();
```
- **CÓDIGO CORREGIDO:** Agregar un scope en el modelo `User`:
```php
// User.php
public function scopeAgents(Builder $query): Builder
{
    return $query->whereIn('role', ['agent', 'admin']);
}
```
Y usar en el controlador:
```php
$agents = User::agents()->get();
```
- **RAZÓN:** DRY y mantenibilidad. Si las reglas de negocio cambian (ej. nuevo rol `supervisor`), se modifica en un solo lugar.

---

**PROBLEMA 12**

- **ARCHIVO:** `TicketController.php`
- **LÍNEA:** 23
- **SEVERIDAD:** 🟡 ADVERTENCIA
- **PROBLEMA:** `index()` muestra TODOS los tickets sin filtrar por usuario. Un cliente puede ver los tickets de otros clientes.
- **CÓDIGO ACTUAL:**
```php
$tickets = Ticket::with(['user', 'agent', 'category'])
    ->when(...)
    ->latest()->paginate(10);
```
- **CÓDIGO CORREGIDO:**
```php
$user    = Auth::user();
$tickets = Ticket::with(['user', 'agent', 'category'])
    ->when($user->role === 'client', fn($q) => $q->where('user_id', $user->id))
    ->when(request('status'), fn($q, $s) => $q->where('status', $s))
    ->when(request('search'), function ($q, $search) {
        $safe = str_replace(['%', '_'], ['\%', '\_'], $search);
        $q->where('title', 'like', "%{$safe}%");
    })
    ->latest()->paginate(10)->withQueryString();
```
- **RAZÓN:** Violación de privacidad. Los clientes no deberían poder ver incidentes ajenos.

---

**PROBLEMA 13**

- **ARCHIVO:** `TicketController.php`
- **LÍNEA:** 99–104
- **SEVERIDAD:** 🔴 CRÍTICO
- **PROBLEMA:** La autorización en `destroy()` se hace manualmente con `if/else` en el controlador en lugar de usar la `TicketPolicy`. Además, **la Policy existe pero todos sus métodos retornan `false`** — está completamente inoperativa.
- **CÓDIGO ACTUAL:**
```php
public function destroy(Ticket $ticket)
{
    if (Auth::id() !== $ticket->user_id && Auth::user()->role !== 'admin') {
        return redirect()->route('tickets.index')
            ->with('error', 'No tienes permisos...');
    }
    $ticket->delete();
    ...
}
```
- **CÓDIGO CORREGIDO:** Primero, implementar la Policy:
```php
// TicketPolicy.php
public function delete(User $user, Ticket $ticket): bool
{
    return $user->role === 'admin' || $user->id === $ticket->user_id;
}
```
Luego, en el controlador:
```php
public function destroy(Ticket $ticket)
{
    $this->authorize('delete', $ticket);
    $ticket->delete();
    return redirect()->route('tickets.index')
        ->with('warning', 'Ticket enviado a la papelera.');
}
```
- **RAZÓN:** La lógica de autorización dispersa en el controlador es difícil de auditar. La Policy centraliza y estandariza el acceso.

---

**PROBLEMA 14**

- **ARCHIVO:** `TicketController.php`
- **LÍNEA:** 52–60
- **SEVERIDAD:** 🟢 SUGERENCIA
- **PROBLEMA:** El `try/catch` genérico en `store()` captura cualquier excepción silenciosamente, incluyendo errores de programación. Esto oculta bugs durante el desarrollo.
- **CÓDIGO ACTUAL:**
```php
try {
    Ticket::create($validated);
    return redirect()->route('tickets.index')->with('success', '...');
} catch (\Exception $e) {
    return redirect()->back()->with('error', 'No se pudo crear...');
}
```
- **CÓDIGO CORREGIDO:** Remover el try/catch. Laravel ya maneja excepciones elegantemente. Si se necesita, capturar excepciones específicas:
```php
Ticket::create($validated);
return redirect()->route('tickets.index')->with('success', 'Ticket creado con éxito.');
```
- **RAZÓN:** `catch (\Exception $e)` traga errores reales (ej. fallo de DB, errores de tipo). Usar el handler de Laravel.

---

### `CommentController.php`

**PROBLEMA 15**

- **ARCHIVO:** `CommentController.php`
- **LÍNEA:** 34–37
- **SEVERIDAD:** 🟡 ADVERTENCIA
- **PROBLEMA:** La validación de permisos ocurre DESPUÉS de obtener `$user`, pero ANTES de la validación del request. El orden correcto es: 1) autenticar, 2) autorizar, 3) validar, 4) actuar.
- **CÓDIGO ACTUAL:**
```php
$user = Auth::user();
if ($user->role !== 'admin' && ...) { abort(403, ...); }
$request->validate(['body' => 'required|string|min:2']);
$ticket->comments()->create([...]);
```
- **CÓDIGO CORREGIDO:** Mover la validación antes de la autorización no es el problema mayor aquí. El problema real es usar **inline validation** en lugar de un `FormRequest`:
```php
// Crear StoreCommentRequest
public function rules(): array {
    return ['body' => ['required', 'string', 'min:2', 'max:2000']];
}
```
- **RAZÓN:** Consistencia con el resto del proyecto. Además, falta `max` en la validación — un usuario puede enviar un comentario de tamaño arbitrario.

---

**PROBLEMA 16**

- **ARCHIVO:** `CommentController.php`
- **LÍNEA:** 77–82
- **SEVERIDAD:** 🔴 CRÍTICO
- **PROBLEMA:** `destroy()` no verifica si el usuario tiene permisos para eliminar el comentario. Cualquier usuario autenticado puede borrar cualquier comentario conociendo su ID.
- **CÓDIGO ACTUAL:**
```php
public function destroy(Ticket $ticket, Comment $comment)
{
    $comment->delete();
    return back()->with('success', 'Comentario eliminado');
}
```
- **CÓDIGO CORREGIDO:**
```php
public function destroy(Ticket $ticket, Comment $comment)
{
    $user = Auth::user();
    if ($user->role !== 'admin' && $user->id !== $comment->user_id) {
        abort(403, 'No tienes permisos para eliminar este comentario.');
    }
    $comment->delete();
    return back()->with('success', 'Comentario eliminado');
}
```
- **RAZÓN:** Vulnerabilidad de control de acceso — cualquier usuario autenticado puede borrar comentarios ajenos con una petición DELETE directa.

---

### `DashboardController.php`

**PROBLEMA 17**

- **ARCHIVO:** `DashboardController.php`
- **LÍNEA:** 21–25
- **SEVERIDAD:** 🔴 CRÍTICO
- **PROBLEMA:** `avgResolutionTime` carga TODOS los tickets resueltos en memoria para calcular un promedio en PHP. En producción con miles de tickets, esto causa `Out of Memory`.
- **CÓDIGO ACTUAL:**
```php
$avgResolutionTime = Ticket::whereNotNull('resolved_at')
    ->get()
    ->avg(function ($ticket) {
        return $ticket->created_at->diffInHours($ticket->resolved_at);
    });
```
- **CÓDIGO CORREGIDO:** Calcular en la base de datos:
```php
$avgResolutionTime = Ticket::whereNotNull('resolved_at')
    ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
    ->value('avg_hours');
```
- **RAZÓN:** Este es el problema de rendimiento más grave del proyecto. La regla es: nunca traer colecciones completas a PHP para calcular agregados — usar SQL.

---

**PROBLEMA 18**

- **ARCHIVO:** `DashboardController.php`
- **LÍNEA:** 14–51
- **SEVERIDAD:** 🟡 ADVERTENCIA
- **PROBLEMA:** El dashboard ejecuta 7 queries independientes. Deberían agruparse en una clase Service o usar Query Builder eficiente.
- **RAZÓN:** No es un N+1, pero la lógica de negocio (métricas) está en el controlador. En proyectos más grandes debería moverse a un `DashboardService`.

---

### `ProfileController.php`

**PROBLEMA 19**

- **ARCHIVO:** `ProfileController.php`
- **LÍNEA:** 53
- **SEVERIDAD:** 🟡 ADVERTENCIA
- **PROBLEMA:** Se llama `$user->delete()` después de `Auth::logout()`. Con `SoftDeletes`, esto marca `deleted_at`, lo cual es correcto. Pero el orden importa: si `delete()` falla, la sesión ya fue cerrada. El usuario queda deslogueado pero su cuenta sigue activa.
- **CÓDIGO CORREGIDO:** Invertir el orden:
```php
$user = $request->user();
$user->delete(); // primero
Auth::logout();  // luego
$request->session()->invalidate();
$request->session()->regenerateToken();
return Redirect::to('/');
```

---

## 📁 POLICIES

### `TicketPolicy.php`

**PROBLEMA 20**

- **ARCHIVO:** `TicketPolicy.php`
- **LÍNEAS:** 14–65
- **SEVERIDAD:** 🔴 CRÍTICO
- **PROBLEMA:** **TODOS los métodos de la Policy retornan `false` hardcodeado**. La Policy fue generada con `artisan make:policy` pero nunca implementada. No se usa en ningún controlador (`$this->authorize()` está ausente en `TicketController` salvo en `destroy()`).
- **CÓDIGO ACTUAL:**
```php
public function viewAny(User $user): bool { return false; }
public function view(User $user, Ticket $ticket): bool { return false; }
public function create(User $user): bool { return false; }
public function update(User $user, Ticket $ticket): bool { return false; }
```
- **CÓDIGO CORREGIDO:**
```php
public function viewAny(User $user): bool
{
    return true; // Todos los autenticados pueden listar (filtrado en Controller)
}

public function view(User $user, Ticket $ticket): bool
{
    return $user->role === 'admin'
        || $user->role === 'agent'
        || $user->id === $ticket->user_id;
}

public function create(User $user): bool
{
    return true; // Cualquier autenticado puede abrir un ticket
}

public function update(User $user, Ticket $ticket): bool
{
    return $user->role === 'admin'
        || $user->role === 'agent'
        || $user->id === $ticket->user_id;
}

public function delete(User $user, Ticket $ticket): bool
{
    return $user->role === 'admin' || $user->id === $ticket->user_id;
}
```
- **RAZÓN:** Una Policy con `false` en todos los métodos es inútil y da falsa sensación de seguridad. El sistema actualmente basa toda la autorización en checks manuales dispersos.

---

## 📁 FORM REQUESTS

### `StoreTicketRequest.php`

**PROBLEMA 21**

- **ARCHIVO:** `StoreTicketRequest.php`
- **LÍNEA:** 15
- **SEVERIDAD:** 🟡 ADVERTENCIA
- **PROBLEMA:** `authorize()` retorna `true` hardcodeado sin verificar si el usuario está autenticado. Aunque la ruta tiene middleware `auth`, centralizar la autorización aquí es una buena práctica.
- **CÓDIGO CORREGIDO:**
```php
public function authorize(): bool
{
    return Auth::check();
}
```

---

**PROBLEMA 22**

- **ARCHIVO:** `StoreTicketRequest.php`
- **LÍNEA:** 29
- **SEVERIDAD:** 🟡 ADVERTENCIA
- **PROBLEMA:** No se valida `agent_id` en el store. El formulario `create.blade.php` incluye un select de `agent_id`, pero si se envía un ID arbitrario (ej. un usuario `client`), se asignará sin restricción.
- **CÓDIGO CORREGIDO:**
```php
'agent_id' => 'nullable|exists:users,id',
```
Adicionalmente, en el controlador verificar que el `agent_id` pertenece a un agente o admin.

---

### `UpdateTicketRequest.php`

**PROBLEMA 23**

- **ARCHIVO:** `UpdateTicketRequest.php`
- **LÍNEA:** 34
- **SEVERIDAD:** 🟡 ADVERTENCIA
- **PROBLEMA:** `'due_date' => 'sometimes|nullable|date|after_or_equal:today'` — la regla `after_or_equal:today` impide actualizar un ticket cuya `due_date` ya pasó (para corregirla, por ejemplo). Un admin que quiere cambiar una fecha vencida no puede.
- **CÓDIGO CORREGIDO:**
```php
'due_date' => 'sometimes|nullable|date',
```
O si el negocio lo requiere, solo aplicar la restricción al crear.

---
