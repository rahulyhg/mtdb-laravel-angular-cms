<!DOCTYPE html>
<html>

@inject('utils', 'App\Services\PrerenderUtils')

<head>
    <meta charset="UTF-8">

    <title>{{ $utils->getMovieTitle($data['title']) }}</title>

    <meta name="google" content="notranslate">
    <link rel="canonical" href="{{$utils->getMediaItemUrl($data['title'])}}">

    <meta itemprop="name" content="{{ $utils->getMovieTitle($data['title']) }}">
    <meta name="keywords" content="{{$utils->getKeywords('movie')}}">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $utils->getMovieTitle($data['title']) }}">
    <meta name="twitter:url" content="{{$utils->getMediaItemUrl($data['title']) }}">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $utils->getMovieTitle($data['title']) }}">
    <meta property="og:url" content="{{$utils->getMediaItemUrl($data['title']) }}">
    <meta property="og:site_name" content="{{ $utils->getSiteName() }}">
    <meta property="og:type" content="website">

    <meta property="og:description" content="{{ $utils->getDescription('movie', 'movie_description', $data['title']['description']) }}">
    <meta itemprop="description" content="{{ $utils->getDescription('movie', 'movie_description', $data['title']['description']) }}">
    <meta property="description" content="{{ $utils->getDescription('movie', 'movie_description', $data['title']['description']) }}">
    <meta name="twitter:description" content="{{ $utils->getDescription('movie', 'movie_description', $data['title']['description']) }}">
</head>

<body>
@include('prerender.menu')

<h1>{{ $utils->getMovieTitle($data['title']) }}</h1>

<img src="{{$utils->getMediaImage($data['title'])}}" alt="Title poster" width="270px">

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
    <h3>{{__('Genres')}}</h3>
    <ul>
        @foreach($data['title']['genres'] as $genre)
            <li><a href="{{url('browse?genres='.$genre['name'])}}">{{$genre['name']}}</a></li>
        @endforeach
    </ul>
</div>

<dl>
    <dt>{{__('User Rating')}}</dt>
    <dd>{{$data['title']['rating']}}</dd>

    <dt>{{__('Running Time')}}</dt>
    <dd>{{$data['title']['runtime']}}</dd>

    @if($data['title']['episode_count'])
        <dt>{{__('Episodes')}}</dt>
        <dd>{{$data['title']['episode_count']}}</dd>
    @endif

    @if($data['title']['certification'])
        <dt>{{__('Certification')}}</dt>
        <dd>{{$data['title']['certification']}}</dd>
    @endif

    @if($data['title']['tagline'])
        <dt>{{__('Tagline')}}</dt>
        <dd>{{$data['title']['tagline']}}</dd>
    @endif

    @if($data['title']['original_title'])
        <dt>{{__('Original Title')}}</dt>
        <dd>{{$data['title']['original_title']}}</dd>
    @endif

    <dt>{{__('Release Date')}}</dt>
    <dd>{{$data['title']['release_date']}}</dd>

    @if($data['title']['is_series'])
        <dt>{{__('Budget')}}</dt>
        <dd>{{$data['title']['budget']}}</dd>

        <dt>{{__('Revenue')}}</dt>
        <dd>{{$data['title']['revenue']}}</dd>
    @endif
</dl>

<p>{{ $utils->getDescription('movie', 'movie_description', $data['title']['description']) }}</p>

<div>
    <h3>{{__('Credits')}}</h3>
    <ul style="display: flex; flex-wrap: wrap;">
        @foreach($data['title']['credits'] as $credit)
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

@if(isset($data['title']['videos']))
    <div>
        <h3>{{__('Videos')}}</h3>
        <ul style="display: flex; flex-wrap: wrap">
            @foreach($data['title']['videos'] as $video)
                <li>
                    <figure>
                        <img src="{{$utils->getMediaImage($video['thumbnail'])}}" alt="Video thumbnail" width="180px">
                        <figcaption>{{$video['name']}}</figcaption>
                    </figure>
                </li>
            @endforeach
        </ul>
    </div>
@endif

@if(isset($data['title']['reviews']))
    <div>
        <h3>{{__('Reviews')}}</h3>
        <ul style="display: flex; flex-wrap: wrap">
            @foreach($data['title']['reviews'] as $review)
                @if($review['type'] === 'user')
                    <li>
                        <h4>{{$review['author']}}</h4>
                        <p>{{$review['body']}}</p>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
@endif

<div>
    <h3>{{__('Images')}}</h3>
    <ul style="display: flex; flex-wrap: wrap">
        @foreach($data['title']['images'] as $image)
            <li><img src="{{$utils->getMediaImage($image)}}" alt="Media image" width="270px"></li>
        @endforeach
    </ul>
</div>

</body>
</html>
