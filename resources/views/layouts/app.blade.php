<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'My App')</title>

    {{-- Vite (Tailwind v4 + JS) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine (kalau belum ada di app.js) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- STACK styles --}}
    @stack('styles')
</head>

<body class="h-full bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-white antialiased">

    {{-- NAVBAR --}}
    @include('homepage.navbar')

    {{-- MAIN --}}
    <main class="min-h-screen px-4 py-6">
        <div class="max-w-7xl mx-auto">
            @yield('content')
        </div>
    </main>

    {{-- FOOTER (optional tapi bagus) --}}
    <footer class="border-t border-gray-200 dark:border-gray-800 py-6 mt-10">
        <div class="text-center text-sm text-gray-500">
            © {{ date('Y') }} My App. All rights reserved.
        </div>
    </footer>

    {{-- STACK scripts --}}
    @stack('scripts')

</body>
</html>