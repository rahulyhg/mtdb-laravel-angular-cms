<!DOCTYPE html>
<html>

@inject('utils', 'App\Services\PrerenderUtils')

<head>
    <meta charset="UTF-8">

    <title>{{ $utils->getTitle('people') }}</title>

    <meta name="google" content="notranslate">
    <link rel="canonical" href="{{ url('people') }}">

    <meta itemprop="name" content="{{ $utils->getTitle('people') }}">
    <meta name="keywords" content="{{$utils->getKeywords('people')}}">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $utils->getTitle('people') }}">
    <meta name="twitter:url" content="{{ url('people') }}">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $utils->getTitle('people') }}">
    <meta property="og:url" content="{{ url('people') }}">
    <meta property="og:site_name" content="{{ $utils->getSiteName() }}">
    <meta property="og:type" content="website">

    <meta property="og:description" content="{{ $utils->getDescription('people') }}">
    <meta itemprop="description" content="{{ $utils->getDescription('people') }}">
    <meta property="description" content="{{ $utils->getDescription('people') }}">
    <meta name="twitter:description" content="{{ $utils->getDescription('people') }}">
</head>

<body>
@include('prerender.menu')

<h1>{{ __('Popular People') }}</h1>

<ul style="display: flex; flex-wrap: wrap;">
    @foreach($data as $person)
        <li>
            <img src="{{$utils->getMediaImage($person)}}" alt="Person poster" width="270px">
            <h3>
                <a href="{{$utils->getMediaItemUrl($person)}}">{{$person['name']}}</a>
            </h3>
            <div>{{$person['known_for']}}</div>
            @if(isset($person['popular_credits'][0]))
                <div>
                    <a href="{{$utils->getMediaItemUrl($person['popular_credits'][0])}}">{{$person['popular_credits'][0]['name']}}</a>
                </div>
            @endif
            <p>{{$person['description']}}</p>
        </li>
    @endforeach

    {{ $data->links() }}
</ul>

</body>
</html>
