<!DOCTYPE html>
<html>

@inject('utils', 'App\Services\PrerenderUtils')

<head>
    <meta charset="UTF-8">

    <title>{{ $utils->getSeasonTitle($data['title'], $data['title']['season']) }}</title>

    <meta name="google" content="notranslate">
    <link rel="canonical" href="{{$utils->getMediaItemUrl($data['title']['season'])}}">

    <meta itemprop="name" content="{{$utils->getSeasonTitle($data['title'], $data['title']['season'])}}">
    <meta name="keywords" content="{{$utils->getKeywords('movie')}}">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $utils->getSeasonTitle($data['title'], $data['title']['season']) }}">
    <meta name="twitter:url" content="{{$utils->getMediaItemUrl($data['title']['season'])}}">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $utils->getSeasonTitle($data['title'], $data['title']['season']) }}">
    <meta property="og:url" content="{{$utils->getMediaItemUrl($data['title']['season'])}}">
    <meta property="og:site_name" content="{{ $utils->getSiteName() }}">
    <meta property="og:type" content="website">

    <meta property="og:description" content="{{ $utils->getDescription('movie', 'movie_description', $data['title']['description']) }}">
    <meta itemprop="description" content="{{ $utils->getDescription('movie', 'movie_description', $data['title']['description']) }}">
    <meta property="description" content="{{ $utils->getDescription('movie', 'movie_description', $data['title']['description']) }}">
    <meta name="twitter:description" content="{{ $utils->getDescription('movie', 'movie_description', $data['title']['description']) }}">
</head>

<body>
@include('prerender.menu')

<h1>{{$data['title']['name']}}: {{__('Season')}} {{$data['title']['season']['number']}}</h1>

@if(isset($data['title']['seasons']))
    <div>
        <h3>{{__('Seasons')}}</h3>
        <ul>
            @foreach($data['title']['seasons'] as $season)
                <li><a href="{{$utils->getMediaItemUrl($season)}}">{{$season['number']}}</a></li>
            @endforeach
        </ul>
    </div>
@endif

<div>
    <ul>
        @foreach($data['title']['season']['episodes'] as $episode)
            <li>
                <figure>
                    <img src="{{$utils->getMediaImage($episode)}}" alt="Episode poster" width="270px">
                    <figcaption>
                        <a href="{{$utils->getMediaItemUrl($episode)}}">{{$episode['name']}}</a>
                    </figcaption>
                </figure>
            </li>
        @endforeach
    </ul>
</div>

</body>
</html>
