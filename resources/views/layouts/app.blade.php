<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Очумелые ручки')</title>
    @yield('styles')
</head>
<body class="@yield('body_class')">
    @yield('content')
</body>
</html>