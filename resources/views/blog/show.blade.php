@extends('layout.main-layout')

@section('title')
    {{ $blog->name }}
@endsection

@section('content')
    <p>{{ $blog->text }}</p>
    @if(auth()->user()->type == 'admin')
        <button id="create_art_button" class="btn btn-primary">New article</button>
        <div style="display: none;" class="mt-2 mb-2" id="new_article">
            {!! Form::hidden('blog_id', $blog->id, ['id' => 'form-blog-id']) !!}
            {!! Form::hidden('article_id', 0, ['id' => 'form-article-id']) !!}
            <div class="form-group">
                <label>Name</label>
                {!! Form::text('name', null, ['class' => 'form-control', 'id' => 'form-article-name']) !!}
                <div id="validationName" class="invalid-feedback"></div>
            </div>
            <div class="form-group">
                <label>Text</label>
                {!! Form::textarea('text', null, ['class' => 'form-control', 'id' => 'form-article-text', 'autocomplete' => 'off']) !!}
                <div id="validationText" class="invalid-feedback"></div>
            </div>
            <button id="form-create-button" data-method="POST" data-ajax-url="" type="button" class="btn btn-primary">
                Create
            </button>
        </div>
        <div class="pt-2"></div>
    @endif
    @if(!empty($blog->articles))
        <table class="table">
            <thead>
            <tr>
                <th scope="col">Date</th>
                <th scope="col">Name</th>
                @if(auth()->user()->type == 'admin')
                    <th scope="col">Actions</th>
                @endif
            </tr>
            </thead>
            <tbody id="articles">
            @foreach($blog->articles->sortByDesc('updated_at') as $article)
                <tr id="article_row_{{ $article->id }}">
                    <td id="article_row_date_{{ $article->id }}">{{ $article->updated_at->format('d.m.Y H:i') }}</td>
                    <td>
                        <a
                            id="article_row_name_{{ $article->id }}"
                            href="{{ route('blog.article.show', [$blog, $article]) }}"
                        >
                            {{ $article->name }}
                        </a>
                    </td>
                    @if(auth()->user()->type == 'admin')
                        <td>
                            <button
                                type="button"
                                class="del_art_button btn btn-danger btn-sm"
                                data-id="{{ $article->id }}"
                                data-ajax-url="{{ route('blog.article.delete', [$blog, $article]) }}"
                            >
                                Delete
                            </button>
                            <button
                                type="button"
                                class="edit_art_button btn btn-danger btn-sm"
                                data-id="{{ $article->id }}"
                                data-ajax-url="{{ route('blog.article.get', [$blog, $article]) }}"
                            >
                                Edit
                            </button>
                        </td>
                    @endif
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
@endsection

@push('after-scripts')
    <script>
        let button = $('#form-create-button');
        $('#create_art_button').click(function () {
            $('#form-article-name').val('').removeClass('is-invalid');
            $('#form-article-text').val('').removeClass('is-invalid');
            $('#form-article-id').val(0);
            button.html('Create');
            button.attr('data-ajax-url', '{{ route('blog.article.create', $blog) }}');
            button.attr('data-method', 'POST');
            if ($('#new_article').css('display') == 'none') {
                $('#new_article').toggle();
            }
        });
        $('#form-create-button').click(function () {
            let requestMethod = $(this).data('method');
            let ajaxUrl = $(this).data('ajax-url');
            if (ajaxUrl) {
                $.ajax({
                    url: ajaxUrl,
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'blog_id': $('#form-blog-id').val(),
                        'name': $('#form-article-name').val(),
                        'text': $('#form-article-text').val(),
                        'article_id': $('#form-article-id').val(),
                    },
                    dataType: "json",
                    type: requestMethod,
                    cache: false,
                    success: function (response) {
                        if (response.success) {
                            $('#new_article').toggle();
                            if (response.action === 'update') {
                                $('#article_row_date_' + response.article.id).html(response.article.updated_at);
                                $('#article_row_name_' + response.article.id).html(response.article.name);
                            } else if (response.action === 'store') {
                                let row =
                                    '<tr>' +
                                        '<td>' + response.article.updated_at + '</td>' +
                                        '<td>' + response.article.name + '</td>' +
                                        '<td>' + '</td>' +
                                    '</tr>'
                                ;
                                $('#articles').prepend(row);
                            }
                        }
                    },
                    error: function (response) {
                        console.log(response);
                        response = response.responseJSON;
                        if (response.errors.name) {
                            $('#form-article-name').addClass('is-invalid');
                            $('#validationName').html(response.errors.name[0]);
                        }
                        if (response.errors.text) {
                            $('#form-article-text').addClass('is-invalid');
                            $('#validationText').html(response.errors.text[0]);
                        }
                    }
                });
            }
        });
        $('.edit_art_button').click(function () {
            let ajaxUrl = '{{ route('blog.article.update', [$blog, ':id:']) }}'
            let articleId = $(this).data('id');
            $('#form-article-id').val(articleId);
            ajaxUrl = ajaxUrl.replace(':id:', articleId);
            let getCurrentArticle = $(this).data('ajax-url');
            if (getCurrentArticle) {
                $.ajax({
                    url: getCurrentArticle,
                    data: {
                        '_token': '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    type: "GET",
                    cache: false,
                    success: function (response) {
                        if (!response.success) {
                            alert('Something went wrong');
                        } else {
                            $('#form-article-name').val(response.article.name);
                            $('#form-article-text').val(response.article.text);
                            $('#form-article-id').val(response.article.id);
                        }
                    },
                    error: function () {
                        alert('Check your connection');
                    }
                });
            }
            button.html('Edit');
            button.attr('data-ajax-url', ajaxUrl);
            button.attr('data-method', 'PATCH');
            $([document.documentElement, document.body]).animate({
                scrollTop: $("#create_art_button").offset().top
            }, 1000);
            if ($('#new_article').css('display') == 'none') {
                $('#new_article').toggle();
            }
        });
        $('.del_art_button').click(function () {
            let articleId = $(this).data('id');
            let ajaxUrl = $(this).data('ajax-url');
            if (confirmLink('Do you really want delete this article?') && ajaxUrl) {
                $.ajax({
                    url: ajaxUrl,
                    data: {
                        '_token': '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    type: "DELETE",
                    cache: false,
                    success: function (response) {
                        if (!response.success) {
                            alert('Something went wrong');
                        } else {
                            $('#article_row_' + response.article.id).remove();
                        }
                    },
                    error: function () {
                        alert('Check your connection');
                    }
                });
            }
        });
    </script>
@endpush
