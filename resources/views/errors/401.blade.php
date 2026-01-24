@extends('errors::layout')

@section('title', __('Unauthorized'))

@section('content')
<div class="max-w-md mx-auto text-center">
    <!-- Error Icon -->
    <div class="mb-8">
        <div class="w-24 h-24 mx-auto bg-linear-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center shadow-lg">
            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
    </div>

    <!-- Error Title -->
    <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
        Access <span class="bg-linear-to-r from-red-600 to-red-500 bg-clip-text text-transparent">Unauthorized</span>
    </h1>

    <!-- Error Message -->
    <p class="text-lg text-gray-600 dark:text-gray-300 mb-8">
        You need to be logged in to access this resource.
    </p>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ route('login') }}"
           class="inline-flex items-center px-6 py-3 bg-linear-to-r from-purple-600 to-purple-700 text-white font-medium rounded-lg hover:from-purple-700 hover:to-purple-800 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
            Sign In
        </a>

        <a href="{{ route('register') }}"
           class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-800 text-gray-900 dark:text-white font-medium rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            Sign Up
        </a>
    </div>

    <!-- Additional Help -->
    <div class="mt-8 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Don't have an account? <a href="{{ route('register') }}" class="text-purple-600 hover:text-purple-500 font-medium">Create one here</a>.
        </p>
    </div>
</div>
@endsection
