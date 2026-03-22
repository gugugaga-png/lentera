<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'My App')</title>

    @vite('resources/css/app.css')
    
    {{-- Tempat untuk styles dari halaman --}}
    @stack('styles')
</head>
<body class="bg-black text-white ">

    {{-- Navbar --}}
    @include('homepage.navbar')

    {{-- Konten halaman --}}
    <main class="min-h-screen">
        @yield('content')
    </main>

    {{-- Tempat untuk scripts dari halaman --}}
    @stack('scripts')
</body>
</html>