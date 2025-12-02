@extends('layouts.page')

@section('title', $page->title)

@section('page-title', $page->title)

@section('page-description', $page->meta_description)

@section('page-content')
<div class="max-w-4xl mx-auto">
    <div class="card bg-white dark:bg-gray-800 rounded-xl p-6 md:p-8 shadow-sm">
        <div class="prose prose-lg max-w-none dark:prose-invert">
            {!! $page->content !!}
        </div>
    </div>
</div>
@endsection