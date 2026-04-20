<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Synapso - Dashboard</title>

<script src="https://cdn.tailwindcss.com"></script>

<script>
tailwind.config = {
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
      }
    }
  }
}
</script>

</head>

<body class="bg-synapso-bg font-sans antialiased">

<div class="min-h-screen flex flex-col">

<!-- NAVBAR -->
<nav class="bg-synapso-navy text-white p-4 flex justify-between items-center">
    <div class="flex items-center space-x-6">
        <span class="text-2xl font-bold">Synapso</span>

        <div class="hidden md:flex space-x-6 text-sm">
            <a href="#" class="hover:text-synapso-gold">Dashboard</a>
            <a href="#" class="hover:text-synapso-gold">Tickets</a>
            <a href="#" class="hover:text-synapso-gold">Reportes</a>
        </div>
    </div>

    <div class="flex items-center space-x-4">

        <!-- BOTÓN PRIMARIO -->
        <button onclick="showToast()" class="bg-synapso-gold px-4 py-2 rounded-lg text-white font-semibold hover:opacity-90">
            + Crear Ticket
        </button>

        <div class="w-10 h-10 bg-slate-600 rounded-full flex items-center justify-center border border-white text-sm">
            KA
        </div>
    </div>
</nav>

<!-- MAIN -->
<main class="p-6 flex-grow max-w-7xl mx-auto w-full">

<!-- MÉTRICAS -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">

    <div class="bg-white p-4 rounded-lg border border-slate-200">
        <p class="text-slate-500 text-sm font-semibold uppercase">Tickets Abiertos</p>
        <p class="text-3xl font-bold text-synapso-navy">24</p>
    </div>

    <div class="bg-white p-4 rounded-lg border border-slate-200">
        <p class="text-slate-500 text-sm font-semibold uppercase">Lead Time</p>
        <p class="text-3xl font-bold text-synapso-navy">4.2h</p>
    </div>

    <div class="bg-white p-4 rounded-lg border border-slate-200">
        <p class="text-slate-500 text-sm font-semibold uppercase">Cycle Time</p>
        <p class="text-3xl font-bold text-synapso-navy">1.8h</p>
    </div>

    <div class="bg-white p-4 rounded-lg border border-slate-200">
        <p class="text-slate-500 text-sm font-semibold uppercase">Resueltos Hoy</p>
        <p class="text-3xl font-bold text-synapso-success">12</p>
    </div>

</div>

<!-- KANBAN -->
<div class="flex space-x-4 overflow-x-auto pb-4">

<!-- OPEN -->
<div class="w-80 bg-slate-100 p-3 rounded-lg">
    <h3 class="font-bold text-slate-700 mb-4 flex justify-between">
        OPEN <span class="bg-slate-300 px-2 rounded text-sm">3</span>
    </h3>

    <div class="space-y-3">

        <div class="bg-white p-4 rounded-lg border border-slate-200">

            <span class="text-xs font-bold text-synapso-danger uppercase">
                Prioridad Crítica
            </span>

            <p class="font-semibold text-slate-800 mt-1">
                Falla crítica en VPN
            </p>

            <p class="text-sm text-slate-500">Usuario: Roberto M.</p>

            <div class="mt-4 flex justify-between items-center">
                <span class="text-xs text-slate-400">Hace 15 min</span>

                <button class="text-synapso-gold text-sm font-bold hover:underline">
                    Tomar Caso
                </button>
            </div>

        </div>

    </div>
</div>

<!-- IN PROGRESS -->
<div class="w-80 bg-slate-100 p-3 rounded-lg">
    <h3 class="font-bold text-slate-700 mb-4 flex justify-between">
        IN PROGRESS <span class="bg-slate-300 px-2 rounded text-sm">2</span>
    </h3>

    <div class="space-y-3">

        <div class="bg-white p-4 rounded-lg border border-slate-200">

            <span class="text-xs font-bold text-synapso-gold uppercase">
                Prioridad Alta
            </span>

            <p class="font-semibold text-slate-800 mt-1">
                Configuración Outlook
            </p>

            <p class="text-sm text-slate-500">Agente: Alexis C.</p>

        </div>

    </div>
</div>

<!-- ON HOLD -->
<div class="w-80 bg-slate-100 p-3 rounded-lg">
    <h3 class="font-bold text-slate-700 mb-4 flex justify-between">
        ON HOLD <span class="bg-slate-300 px-2 rounded text-sm">1</span>
    </h3>

    <div class="bg-white p-4 rounded-lg border border-slate-200">
        <p class="text-slate-500 text-sm">Esperando respuesta del usuario</p>
    </div>
</div>

<!-- RESOLVED -->
<div class="w-80 bg-slate-100 p-3 rounded-lg">
    <h3 class="font-bold text-slate-700 mb-4 flex justify-between">
        RESOLVED <span class="bg-slate-300 px-2 rounded text-sm">15</span>
    </h3>

    <div class="space-y-3 opacity-70">

        <div class="bg-white p-4 rounded-lg border border-slate-200">
            <p class="font-semibold text-slate-800 line-through">
                Recuperación de contraseña
            </p>
        </div>

    </div>
</div>

<!-- CLOSED -->
<div class="w-80 bg-slate-100 p-3 rounded-lg">
    <h3 class="font-bold text-slate-700 mb-4 flex justify-between">
        CLOSED <span class="bg-slate-300 px-2 rounded text-sm">4</span>
    </h3>

    <div class="bg-white p-4 rounded-lg border border-slate-200">
        <p class="text-slate-500 text-sm">Ticket cerrado sin acción</p>
    </div>
</div>

</div>

</main>

</div>

<!-- TOAST -->
<div id="toast" class="hidden fixed bottom-5 right-5 bg-synapso-success text-white px-4 py-2 rounded-lg">
    Ticket creado correctamente
</div>

<script>
function showToast() {
    const toast = document.getElementById('toast');
    toast.classList.remove('hidden');

    setTimeout(() => {
        toast.classList.add('hidden');
    }, 3000);
}
</script>

</body>
</html>