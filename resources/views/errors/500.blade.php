@extends('errors.layout')

@section('title', 'Internal Server Error')

@section('content')
<div class="text-center">
    <!-- Error Icon -->
    <div class="flex justify-center mb-6">
        <div class="h-16 w-16 bg-linear-to-r from-red-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg">
            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
        </div>
    </div>

    <!-- Error Code -->
    <div class="mb-4">
        <h1 class="text-6xl font-bold text-transparent bg-clip-text bg-linear-to-r from-red-500 to-purple-600 mb-2">
            500
        </h1>
        <div class="h-1 bg-linear-to-r from-red-500 to-purple-500 rounded-full w-24 mx-auto mb-4"></div>
    </div>

    <!-- Error Title -->
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
        Internal Server Error
    </h2>

    <!-- Error Description -->
    <p class="text-gray-600 dark:text-gray-300 mb-8 max-w-md mx-auto">
        Something went wrong on our end. We're working to fix this issue. Please try again in a few moments.
    </p>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ url('/') }}" class="inline-flex items-center px-6 py-3 bg-blue-700 hover:bg-blue-600 dark:bg-primary dark:hover:bg-zinc-200 text-white dark:text-gray-900 rounded-lg font-medium transition-colors duration-200 shadow-md hover:shadow-lg">
            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            Go Home
        </a>
        <button onclick="window.location.reload()" class="inline-flex items-center px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg font-medium transition-colors duration-200 shadow-md hover:shadow-lg">
            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Try Again
        </button>
    </div>

    <!-- Additional Help -->
    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            If this problem persists, please <a href="mailto:support@example.com" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 underline">contact support</a> with details about what you were doing.
        </p>
    </div>
</div>
@endsection
