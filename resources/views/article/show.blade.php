@extends('layout.main-layout')

@section('title')
    {{ $article->name }}
@endsection

@section('content')
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('blog.index') }}">Blogs</a></li>
        <li class="breadcrumb-item"><a href="{{ route('blog.show', $article->blog) }}">{{ $article->blog->name }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $article->name }}</li>
      </ol>
    </nav>
    {!! $article->text !!}
@endsection