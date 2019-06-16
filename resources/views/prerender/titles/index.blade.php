<!DOCTYPE html>
<html>

@inject('utils', 'App\Services\PrerenderUtils')

<head>
    <meta charset="UTF-8">

    <title>{{ $utils->getTitle('browse') }}</title>

    <meta name="google" content="notranslate">
    <link rel="canonical" href="{{ url('browse') }}">

    <meta itemprop="name" content="{{ $utils->getTitle('browse') }}">
    <meta name="keywords" content="{{$utils->getKeywords('browse')}}">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $utils->getTitle('browse') }}">
    <meta name="twitter:url" content="{{ url('browse') }}">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $utils->getTitle('browse') }}">
    <meta property="og:url" content="{{ url('browse') }}">
    <meta property="og:site_name" content="{{ $utils->getSiteName() }}">
    <meta property="og:type" content="website">

    <meta property="og:description" content="{{ $utils->getDescription('browse') }}">
    <meta itemprop="description" content="{{ $utils->getDescription('browse') }}">
    <meta property="description" content="{{ $utils->getDescription('browse') }}">
    <meta name="twitter:description" content="{{ $utils->getDescription('browse') }}">
</head>

<body>
@include('prerender.menu')

<h1>{{ __('Browse') }}</h1>

<ul style="display: flex; flex-wrap: wrap;">
    @foreach($data as $title)
        <li>
            <figure>
                <img src="{{$utils->getMediaImage($title)}}" alt="Title poster" width="270px">
                <figcaption>
                    <a href="{{$utils->getMediaItemUrl($title)}}">{{$title['name']}}</a>
                </figcaption>
            </figure>
        </li>
    @endforeach

    {{ $data->links() }}
</ul>

</body>
</html>
