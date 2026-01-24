@extends('errors.layout')

@section('title', 'Page Not Found')

@section('content')
<div class="text-center">
    <!-- Error Icon -->
    <div class="flex justify-center mb-6">
        <div class="h-16 w-16 bg-linear-to-r from-red-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg">
            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.29-.98-5.5-2.5m.5-4.5a7.963 7.963 0 015 2.5m-5-2.5V7a3 3 0 116 0v2.5" />
            </svg>
        </div>
    </div>

    <!-- Error Code -->
    <div class="mb-4">
        <h1 class="text-6xl font-bold text-transparent bg-clip-text bg-linear-to-r from-red-500 to-purple-600 mb-2">
            404
        </h1>
        <div class="h-1 bg-linear-to-r from-red-500 to-purple-500 rounded-full w-24 mx-auto mb-4"></div>
    </div>

    <!-- Error Title -->
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
        Page Not Found
    </h2>

    <!-- Error Description -->
    <p class="text-gray-600 dark:text-gray-300 mb-8 max-w-md mx-auto">
        Sorry, the page you're looking for doesn't exist. It might have been moved, deleted, or you entered the wrong URL.
    </p>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ url('/') }}" class="inline-flex items-center px-6 py-3 bg-blue-700 hover:bg-blue-600 dark:bg-primary dark:hover:bg-zinc-200 text-white dark:text-gray-900 rounded-lg font-medium transition-colors duration-200 shadow-md hover:shadow-lg">
            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            Go Home
        </a>
        <button onclick="history.back()" class="inline-flex items-center px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg font-medium transition-colors duration-200 shadow-md hover:shadow-lg">
            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Go Back
        </button>
    </div>

    <!-- Additional Help -->
    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            If you believe this is an error, please <a href="mailto:support@example.com" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 underline">contact support</a>.
        </p>
    </div>
</div>
@endsection
