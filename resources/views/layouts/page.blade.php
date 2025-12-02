@extends('layouts.app')

@section('content')
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