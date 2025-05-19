<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="alternate icon" type="image/png" href="{{ asset('icon.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <!-- PDF.js -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>

        <style>
            @media print {
                .no-print {
                    display: none !important;
                }
            }
        </style>

    </head>
    @if(session('status'))
    <script>
        setTimeout(() => {
            window.location.reload();
        }, 2000); // refresh po 0.5 sekunde – uprav podľa potreby
    </script>
    @endif     
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
               {{ $slot }}
            </main>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
        <!-- <script src="https://unpkg.com/@flowbite/icons"></script> -->
         @stack('scripts')
    </body>
<!-- Flowbite SVG ikonky -->
<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
  <symbol id="hi-scissors" viewBox="0 0 24 24">
    <path fill="currentColor" d="M14.121 14.121L10 10m0 0L5.879 5.879M10 10l4.121-4.121M10 10L5.879 14.121M16 16l-4 4m4-4l4-4m-4 4l-4-4" />
  </symbol>
  <symbol id="hi-link" viewBox="0 0 24 24">
    <path fill="currentColor" d="M13.828 10.172a4 4 0 00-5.656 0L4.222 14.121a4 4 0 105.656 5.657L13 16m-2-8l5.657-5.657a4 4 0 015.657 5.657L15 14" />
  </symbol>
  <symbol id="hi-lock-closed" viewBox="0 0 24 24">
    <path fill="currentColor" d="M12 15a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
    <path fill="currentColor" fill-rule="evenodd" d="M6 8V6a6 6 0 1112 0v2h1a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2v-9a2 2 0 012-2h1zm2 0h8V6a4 4 0 10-8 0v2z" clip-rule="evenodd" />
  </symbol>
  <symbol id="hi-lock-open" viewBox="0 0 24 24">
    <path fill="currentColor" d="M12 15a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
    <path fill="currentColor" fill-rule="evenodd" d="M17 8h-2V6a3 3 0 00-6 0h2a1 1 0 012 0v2H7a2 2 0 00-2 2v9a2 2 0 002 2h10a2 2 0 002-2v-9a2 2 0 00-2-2z" clip-rule="evenodd" />
  </symbol>
  <symbol id="hi-trash" viewBox="0 0 24 24">
    <path fill="currentColor" d="M6 7h12l-1 14H7L6 7z" />
    <path fill="currentColor" d="M9 4h6v2H9z" />
  </symbol>
  <symbol id="hi-clock" viewBox="0 0 24 24">
    <path fill="currentColor" d="M12 6v6l4 2" />
    <path fill="currentColor" fill-rule="evenodd" d="M12 2a10 10 0 100 20 10 10 0 000-20zm0 2a8 8 0 110 16 8 8 0 010-16z" clip-rule="evenodd" />
  </symbol>
  <symbol id="hi-user-circle" viewBox="0 0 24 24">
    <path fill="currentColor" fill-rule="evenodd" d="M12 2a10 10 0 00-7.071 17.071A9.96 9.96 0 0012 22a9.96 9.96 0 007.071-2.929A10 10 0 0012 2zm0 4a3 3 0 11-2.121 5.121A3 3 0 0112 6zm0 12c-2.21 0-4.21-1.168-5.3-2.91.048-1.604 3.6-2.49 5.3-2.49s5.252.886 5.3 2.49A6.977 6.977 0 0112 18z" clip-rule="evenodd" />
  </symbol>
  <symbol id="hi-book-open" viewBox="0 0 24 24">
    <path fill="currentColor" d="M12 4a1 1 0 00-1 1v13a1 1 0 002 0V5a1 1 0 00-1-1z" />
    <path fill="currentColor" d="M20 5a2 2 0 00-2-2H9v16h9a2 2 0 002-2V5zM4 5a2 2 0 012-2h7v16H6a2 2 0 01-2-2V5z" />
  </symbol>
</svg>

</html>
