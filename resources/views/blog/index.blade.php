@extends('layout.main-layout')

@section('title')
    Список блогов
@endsection

@section('content')
    @if(!empty($blogs))
        <table class="table">
        <thead>
            <tr>
              <th scope="col">Date</th>
              <th scope="col">Last Article</th>
              <th scope="col">Blog</th>
            </tr>
        </thead>
        <tbody>

        @foreach($blogs as $blog)
        <tr>
            <td>{{ $blog->updated_at->format('d.m.Y H:i') }}</td>
            <td>{{ $blog->name }}</td>
            <td>
                <a href="{{ route('blog.show', $blog) }}">{{ Str::limit($blog->text) }}</a>
            </td>
        </tr>
        @endforeach
        </tbody>
        </table>
    @endif
@endsection
