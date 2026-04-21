<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Synapso</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-synapso-bg font-sans antialiased">
    @include('components.navbar')
    <main>
        <!-- <x-alert /> <- agregar cuando creen el componente de alertas en Sprint 1 -->
        @yield('content')
    </main>
</body>

</html>