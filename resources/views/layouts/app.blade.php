<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi aplicación</title>
    
    
    <!-- Agregar Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Agregar DataTables CSS (si lo usas) -->
    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Mi Aplicación</a>
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="{{ url('/debtor/upload') }}">Inicio</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/debtor/show') }}">Deudores</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mt-4 mb-5">
        @yield('content')
    </div>

   <!-- Agregar jQuery -->
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Agregar Bootstrap JS (y Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

<!-- Agregar DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
@yield('scripts')
</body>
</html>
