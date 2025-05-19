<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CookedPDF</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <x-animated-logo></x-animated-logo>
    <nav class="bg-white shadow-sm py-2 px-3 flex justify-between items-center">
        <h1 class="text-xl font-semibold text-gray-800 flex gap-2 items-center">
            <svg xmlns="http://www.w3.org/2000/svg"
                 fill="none" viewBox="0 0 24 24" stroke-width="2"
                 stroke="currentColor"
                 class="w-12 h-12 text-gray-800">
                 <!-- Classic Fire -->
                <path stroke="#D97706" stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.047 8.287 8.287 0 0 0 9 9.601a8.983 8.983 0 0 1 3.361-6.867 8.21 8.21 0 0 0 3 2.48Z" />
                <path stroke="#FBBF24" stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 0 0 .495-7.468 5.99 5.99 0 0 0-1.925 3.547 5.975 5.975 0 0 1-2.133-1.001A3.75 3.75 0 0 0 12 18Z" />
            </svg>
            CookedPDF</h1>
        <div class="space-x-4">
            <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-gray-900">{{__('login')}}</a>
            <a href="{{ route('register') }}" class="text-sm text-yellow-700 hover:text-yellow-800 hover:underline">
                {{__('register')}}
            </a>
        </div>
    </nav>

    <main class="flex items-center justify-center py-20 px-4">
        <div class="max-w-2xl w-full bg-white rounded-lg shadow-md p-8 text-center">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">We are <span class="text-yellow-600 text-3xl">CookedPDF</span></h2>
            <h3>
                <span class="text-lg text-gray-600">{{__('welcome.title1')}}</span>
                <span class="text-lg text-yellow-600"> {{__('welcome.title2')}}</span>
            </h3>

            <p class="text-gray-600">
                {{__('welcome.text')}}
            </p>
            <!-- GIF pod obsahom -->
            <div class="mt-8 flex flex-col gap-4 w-full items-center">
                <img src="{{ asset('images/cat1.gif') }}" alt="Funny cat gif" class="w-4/5 h-auto rounded shadow" />
                <!-- <img src="{{ asset('images/cat2.gif') }}" alt="Cool cat gif" class="w-full h-auto rounded shadow" /> -->
            </div>

        </div>
    </main>
</body>
</html>