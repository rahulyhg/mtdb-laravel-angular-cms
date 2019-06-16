<!DOCTYPE html>
<html>

@inject('utils', 'App\Services\PrerenderUtils')

<head>
    <meta charset="UTF-8">

    <title>{{ $utils->getTitle('news_article') }}</title>

    <meta name="google" content="notranslate">
    <link rel="canonical" href="{{ $utils->getMediaItemUrl($data['article']) }}">

    <meta itemprop="name" content="{{ $utils->getTitle('news_article') }}">
    <meta name="keywords" content="{{$utils->getKeywords('news_article')}}">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $utils->getTitle('news_article') }}">
    <meta name="twitter:url" content="{{ $utils->getMediaItemUrl($data['article']) }}">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $utils->getTitle('news_article') }}">
    <meta property="og:url" content="{{ $utils->getMediaItemUrl($data['article']) }}">
    <meta property="og:site_name" content="{{ $utils->getSiteName() }}">
    <meta property="og:type" content="website">

    <meta property="og:description" content="{{ $utils->getDescription('news_article') }}">
    <meta itemprop="description" content="{{ $utils->getDescription('news_article') }}">
    <meta property="description" content="{{ $utils->getDescription('news_article') }}">
    <meta name="twitter:description" content="{{ $utils->getDescription('news_article') }}">

    @if(isset($data['article']['meta']['source']) && $data['article']['meta']['source'] !== 'local')
        <meta name="robots" content="noindex">
    @endif
</head>

<body>
@include('prerender.menu')

<h1>{{$data['article']['title']}}</h1>

@if(isset($data['article']['meta']['image']))
    <img src="{{$data['article']['meta']['image']}}" alt="Article image">
@endif

<article>
    {!!$data['article']['body']!!}
</article>

</body>
</html>
