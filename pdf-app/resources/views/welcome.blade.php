<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vitaj</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-white shadow-sm py-4 px-6 flex justify-between items-center">
        <h1 class="text-xl font-semibold text-gray-800">Moja aplikácia</h1>
        <div class="space-x-4">
            <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-gray-900">Prihlásiť sa</a>
            <a href="{{ route('register') }}" class="text-sm text-blue-600 hover:underline">Registrovať sa</a>
        </div>
    </nav>

    <main class="flex items-center justify-center py-20 px-4">
        <div class="max-w-2xl w-full bg-white rounded-lg shadow-md p-8 text-center">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Vitaj v našej aplikácii!</h2>
            <p class="text-gray-600 text-lg">Začni používať našu službu a prihlás sa alebo si vytvor nový účet.</p>
        </div>
    </main>
</body>
</html>