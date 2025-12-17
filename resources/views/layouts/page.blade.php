@extends('layouts.app')

@section('content')
<!-- Background Animation Elements -->
<div class="fixed inset-0 pointer-events-none overflow-hidden -z-10">
    <div class="absolute top-0 left-0 w-96 h-96 bg-gradient-to-r from-primary to-secondary rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
    <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
    <div class="absolute bottom-0 left-1/2 w-96 h-96 bg-gradient-to-r from-blue-500 to-teal-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>
</div>

<div class="page-header">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl md:text-4xl font-bold">@yield('page-title')</h1>
        @hasSection('page-description')
            <p class="mt-2 text-lg opacity-90">@yield('page-description')</p>
        @endif
    </div>
</div>

<div class="container mx-auto px-4 sm:px-6 lg:px-8 pb-16">
    @yield('page-content')
</div>
@endsection