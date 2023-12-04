<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('titulo')</title>
     <!-- Bootstrap CSS v5.2.1 -->
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
     integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
     <!-- FontAwesome -->
     <script src="https://kit.fontawesome.com/0a879c88fe.js" crossorigin="anonymous"></script>
     <!-- Mis estilos -->
     <link rel="stylesheet" href="{{ asset('css/main.css') }}">
     <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
    <header>
        <h1>Bunglebuild S.L.</h1>
    </header>

    @yield('contenido')

    <footer>
        Creado por Valentin AC 
        <br>
        &copy; - 2023
    </footer>
</body>
</html>