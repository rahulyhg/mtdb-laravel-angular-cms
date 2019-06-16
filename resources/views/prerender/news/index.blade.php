<!DOCTYPE html>
<html>

@inject('utils', 'App\Services\PrerenderUtils')

<head>
    <meta charset="UTF-8">

    <title>{{ $utils->getTitle('news_page') }}</title>

    <meta name="google" content="notranslate">
    <link rel="canonical" href="{{ url('news') }}">

    <meta itemprop="name" content="{{ $utils->getTitle('news_page') }}">
    <meta name="keywords" content="{{$utils->getKeywords('news_page')}}">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $utils->getTitle('news_page') }}">
    <meta name="twitter:url" content="{{ url('news') }}">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $utils->getTitle('news_page') }}">
    <meta property="og:url" content="{{ url('news') }}">
    <meta property="og:site_name" content="{{ $utils->getSiteName() }}">
    <meta property="og:type" content="website">

    <meta property="og:description" content="{{ $utils->getDescription('news_page') }}">
    <meta itemprop="description" content="{{ $utils->getDescription('news_page') }}">
    <meta property="description" content="{{ $utils->getDescription('news_page') }}">
    <meta name="twitter:description" content="{{ $utils->getDescription('news_page') }}">
</head>

<body>
@include('prerender.menu')

<h1>{{__('Latest News')}}</h1>

<ul>
    @foreach($data as $newsArticle)
        @if(isset($newsArticle['meta']['source']) && $newsArticle['meta']['source'] === 'local')
            <li>
                <h3> <a href="{{$utils->getMediaItemUrl($newsArticle)}}">{{$newsArticle['title']}}</a></h3>

                @if(isset($newsArticle['meta']['image']))
                    <img src="{{$newsArticle['meta']['image']}}" alt="Article image">
                @endif

                <div>{!!$newsArticle['body']!!}</div>
            </li>
        @endif
    @endforeach

    {{ $data->links() }}
</ul>

</body>
</html>
