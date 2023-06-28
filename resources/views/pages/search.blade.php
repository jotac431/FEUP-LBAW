@extends('layouts.app')

@section('title', "- Search")

{{-- ------------------------------------------------------------------------------------ --}}
@section('scripts')
    <script type="text/javascript" src={{ asset('js/user.js') }}></script>
@endsection

{{-- ------------------------------------------------------------------------------------ --}}

@section('error-handler')
    <div class="alert alert-danger mb-0 text-center" role="alert">
        @foreach ($errors->all() as $error)
            <h4 class="my-3">{{ $error }}</h4 class="my-3">
        @endforeach
    </div>
@endsection

{{-- ------------------------------------------------------------------------------------ --}}

@if ($errors->any())
    @section('content')
        @yield('error-handler')
    @endsection
@else

{{-- ------------------------------------------------------------------------------------ --}}

@section('searchInfo')

@if ($results->isEmpty())

    <div class="alert alert-custom mb-0 text-center" role="alert">
        <h3 class="my-3 text-white">No results found</h3>
    </div>

@else

    <div class="alert alert-dark search-nfo my-3 text-center" role="alert">
        <h3 class="mb-0">Displaying {{ $type }} results for: <i>{{ $query }}</i> </h3>
    </div>

@endif

@endsection

{{-- ------------------------------------------------------------------------------------ --}}

@section('results')

@if ($type === 'articles')

    <section id="articles" class="container">
        @include('partials.content.articles', ['articles' => $results])
    </section>

@else {{-- Users --}}

    <section id="users" class="container">
        @include('partials.user.list', ['users' => $results])
    </section>

@endif

@endsection

{{-- ------------------------------------------------------------------------------------ --}}

@section('load-more')
<div id="load-more">

    <button onclick="loadMoreSearch('{{ $type }}', '{{ $query }}')">Load more</button>

</div>
@endsection

{{-- ------------------------------------------------------------------------------------ --}}

@section('content')
    @yield('searchInfo')
    @yield('results')

    @if ($canLoadMore)
        @yield('load-more')
    @endif

@endsection
@endif
