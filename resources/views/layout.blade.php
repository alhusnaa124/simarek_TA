<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs" defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://unpkg.com/flowbite@latest/dist/flowbite.js"></script>
    <style>
        body {
            display: flex;
            margin: 0;
            min-height: 100vh;
        }
        .content {
            margin-left: 250px; /* Space for the sidebar */
            flex-grow: 1;
            width: calc(100% - 250px);
        }
        
    </style>
</head>
<body class="bg-gray-100">

    <!-- Sidebar -->
    @include('layouts.sidebar')

    <!-- Main Content -->
    <div class="content">
        <!-- Top Navigation -->
        @include('layouts.navigation')

        <!-- Page Content -->
        <div class="p-4">
            @yield('content')
        </div>
    </div>
    @stack('scripts')

</body>
</html>