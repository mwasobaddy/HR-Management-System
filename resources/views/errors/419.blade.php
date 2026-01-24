@extends('errors::layout')

@section('title', __('Page Expired'))

@section('content')
<div class="max-w-md mx-auto text-center">
    <!-- Error Icon -->
    <div class="mb-8">
        <div class="w-24 h-24 mx-auto bg-linear-to-br from-yellow-500 to-orange-500 rounded-full flex items-center justify-center shadow-lg">
            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
    </div>

    <!-- Error Title -->
    <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
        Page <span class="bg-linear-to-r from-yellow-600 to-orange-500 bg-clip-text text-transparent">Expired</span>
    </h1>

    <!-- Error Message -->
    <p class="text-lg text-gray-600 dark:text-gray-300 mb-8">
        Your session has expired. Please refresh the page and try again.
    </p>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <button onclick="window.location.reload()"
                class="inline-flex items-center px-6 py-3 bg-linear-to-r from-purple-600 to-purple-700 text-white font-medium rounded-lg hover:from-purple-700 hover:to-purple-800 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Refresh Page
        </button>

        <a href="{{ route('dashboard') }}"
           class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-800 text-gray-900 dark:text-white font-medium rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Go Home
        </a>
    </div>

    <!-- Additional Help -->
    <div class="mt-8 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            If this problem persists, try clearing your browser cache and cookies.
        </p>
    </div>
</div>
@endsection
