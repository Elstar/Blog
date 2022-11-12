<!DOCTYPE html>
<html lang="en">
<head>
    @include('layout.partials.head')
</head>
<body>

@include('layout.partials.nav')

<div class="container">
    @include('layout.partials.messages')

    @yield('content')
</div>

@include('layout.partials.footer')

@stack('before-scripts')
@include('layout.partials.footer-scripts')
@stack('after-scripts')
</body>
</html>
