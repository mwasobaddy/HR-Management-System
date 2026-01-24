@extends('errors::layout')

@section('title', __('Workspace Not Found'))

@section('content')
<div class="max-w-md mx-auto text-center">
    <!-- Error Icon -->
    <div class="mb-8">
        <div class="w-24 h-24 mx-auto bg-linear-to-br from-orange-500 to-orange-600 rounded-full flex items-center justify-center shadow-lg">
            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
        </div>
    </div>

    <!-- Error Title -->
    <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
        Workspace <span class="bg-linear-to-r from-orange-600 to-orange-500 bg-clip-text text-transparent">Not Found</span>
    </h1>

    <!-- Error Message -->
    <p class="text-lg text-gray-600 dark:text-gray-300 mb-8">
        The workspace you're looking for doesn't exist or may have been moved.
    </p>

    <!-- Requested Info -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mb-6">
        <p class="text-sm text-gray-600 dark:text-gray-300">
            <strong class="text-gray-900 dark:text-white">Requested:</strong> {{ $requestedDomain }}
            @if($requestedSubdomain)
                <span class="block text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Subdomain: {{ $requestedSubdomain }}
                </span>
            @endif
        </p>
    </div>

    <!-- Possible Reasons -->
    <div class="text-left mb-8">
        <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">
            This could happen if:
        </p>
        <ul class="text-sm text-gray-600 dark:text-gray-300 space-y-1 ml-4">
            <li>• The URL is incorrect</li>
            <li>• The workspace has been deleted</li>
            <li>• You don't have access to this workspace</li>
        </ul>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col gap-4">
        <a href="{{ route('home') }}"
           class="inline-flex items-center justify-center px-6 py-3 bg-linear-to-r from-purple-600 to-purple-700 text-white font-medium rounded-lg hover:from-purple-700 hover:to-purple-800 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Go to Homepage
        </a>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <a href="{{ route('pricing') }}"
               class="inline-flex items-center justify-center px-4 py-3 bg-white dark:bg-gray-800 text-gray-900 dark:text-white font-medium rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                View Pricing
            </a>

            <a href="{{ route('demo') }}"
               class="inline-flex items-center justify-center px-4 py-3 bg-white dark:bg-gray-800 text-gray-900 dark:text-white font-medium rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Try Demo
            </a>
        </div>
    </div>

    <!-- Additional Help -->
    <div class="mt-8 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
        <p class="text-sm text-gray-600 dark:text-gray-400 text-center">
            Need help? <a href="{{ route('support') }}" class="text-purple-600 hover:text-purple-500 font-medium">Contact Support</a>
        </p>
    </div>
</div>
@endsection