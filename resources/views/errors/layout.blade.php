<!DOCTYPE html>
<html lang="en" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Error')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-sans bg-zinc-50 dark:bg-neutral-900 text-gray-900 dark:text-gray-100">
        <div class="min-h-screen flex items-center justify-center px-4 py-8">
            <div class="max-w-2xl w-full">
                <div class="bg-linear-to-t dark:from-neutral-900 dark:to-neutral-800 from-red-50 to-purple-50 dark:border-neutral-700 border border-gray-200 rounded-xl shadow-xl overflow-hidden">
                    <div class="p-8 sm:p-12">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
        </div>
    </body>
</html>
