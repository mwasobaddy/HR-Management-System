@extends('errors::layout')

@section('title', __('Forbidden'))

@section('content')
<div class="max-w-md mx-auto text-center">
    <!-- Error Icon -->
    <div class="mb-8">
        <div class="w-24 h-24 mx-auto bg-linear-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center shadow-lg">
            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
        </div>
    </div>

    <!-- Error Title -->
    <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
        Access <span class="bg-linear-to-r from-red-600 to-red-500 bg-clip-text text-transparent">Forbidden</span>
    </h1>

    <!-- Error Message -->
    <p class="text-lg text-gray-600 dark:text-gray-300 mb-8">
        {{ $exception->getMessage() ?: 'You don\'t have permission to access this resource.' }}
    </p>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ url()->previous() ?: route('dashboard') }}"
           class="inline-flex items-center px-6 py-3 bg-linear-to-r from-purple-600 to-purple-700 text-white font-medium rounded-lg hover:from-purple-700 hover:to-purple-800 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Go Back
        </a>

        <a href="{{ route('dashboard') }}"
           class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-800 text-gray-900 dark:text-white font-medium rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>
    </div>

    <!-- Additional Help -->
    <div class="mt-8 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            If you believe this is an error, please contact our support team.
        </p>
    </div>
</div>
@endsection
