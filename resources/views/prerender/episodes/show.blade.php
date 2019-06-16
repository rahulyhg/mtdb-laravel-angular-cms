<!DOCTYPE html>
<html>

@inject('utils', 'App\Services\PrerenderUtils')

<head>
    <meta charset="UTF-8">

    <title>{{ $utils->getEpisodeTitle($data['title'], $data['episode']) }}</title>

    <meta name="google" content="notranslate">
    <link rel="canonical" href="{{$utils->getMediaItemUrl($data['episode'])}}">

    <meta itemprop="name" content="{{ $utils->getEpisodeTitle($data['title'], $data['episode']) }}">
    <meta name="keywords" content="{{$utils->getKeywords('movie')}}">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $utils->getEpisodeTitle($data['title'], $data['episode']) }}">
    <meta name="twitter:url" content="{{$utils->getMediaItemUrl($data['episode'])}}">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $utils->getEpisodeTitle($data['title'], $data['episode']) }}">
    <meta property="og:url" content="{{$utils->getMediaItemUrl($data['episode'])}}">
    <meta property="og:site_name" content="{{ $utils->getSiteName() }}">
    <meta property="og:type" content="website">

    <meta property="og:description" content="{{ $utils->getDescription('movie', 'movie_description', $data['episode']['description']) }}">
    <meta itemprop="description" content="{{ $utils->getDescription('movie', 'movie_description', $data['episode']['description']) }}">
    <meta property="description" content="{{ $utils->getDescription('movie', 'movie_description', $data['episode']['description']) }}">
    <meta name="twitter:description" content="{{ $utils->getDescription('movie', 'movie_description', $data['episode']['description']) }}">
</head>

<body>
@include('prerender.menu')

<h1>{{ $utils->getEpisodeTitle($data['title'], $data['episode']) }}</h1>

<img src="{{$utils->getMediaImage($data['episode'])}}" alt="Title poster" width="270px">

<dl>
    <dt>{{__('User Rating')}}</dt>
    <dd>{{$data['episode']['rating']}}</dd>

    <dt>{{__('Running Time')}}</dt>
    <dd>{{$data['episode']['runtime']}}</dd>

    <dt>{{__('Release Date')}}</dt>
    <dd>{{$data['episode']['release_date']}}</dd>
</dl>

<p>{{ $data['episode']['description'] }}</p>

<div>
    <h3>{{__('Credits')}}</h3>
    <ul style="display: flex; flex-wrap: wrap;">
        @foreach($data['episode']['credits'] as $credit)
            <li>
                <figure>
                    <img src="{{$utils->getMediaImage($credit)}}" alt="Credit poster" width="270px">
                    <figcaption>
                        <dl>
                            <dt>{{__('Job')}}</dt>
                            <dd>{{$credit['pivot']['job']}}</dd>
                            <dt>{{__('Department')}}</dt>
                            <dd>{{$credit['pivot']['department']}}</dd>
                            @if($credit['pivot']['character'])
                                <dt>{{__('Character')}}</dt>
                                <dd>{{$credit['pivot']['character']}}</dd>
                            @endif
                        </dl>
                        <a href="{{$utils->getMediaItemUrl($credit)}}">{{$credit['name']}}</a>
                    </figcaption>
                </figure>
            </li>
        @endforeach
    </ul>
</div>

</body>
</html>
