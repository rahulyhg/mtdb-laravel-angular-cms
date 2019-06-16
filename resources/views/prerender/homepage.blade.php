<!DOCTYPE html>
<html>

@inject('utils', 'App\Services\PrerenderUtils')

<head>
    <meta charset="UTF-8">

    <title>{{ $utils->getTitle('homepage') }}</title>

    <meta name="google" content="notranslate">
    <link rel="canonical" href="{{ url('') }}">

    <meta itemprop="name" content="{{ $utils->getTitle('homepage') }}">
    <meta name="keywords" content="{{$utils->getKeywords('homepage')}}">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $utils->getTitle('homepage') }}">
    <meta name="twitter:url" content="{{ url('') }}">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $utils->getTitle('homepage') }}">
    <meta property="og:url" content="{{ url('') }}">
    <meta property="og:site_name" content="{{ $utils->getSiteName() }}">
    <meta property="og:type" content="website">

    <meta property="og:description" content="{{ $utils->getDescription('homepage') }}">
    <meta itemprop="description" content="{{ $utils->getDescription('homepage') }}">
    <meta property="description" content="{{ $utils->getDescription('homepage') }}">
    <meta name="twitter:description" content="{{ $utils->getDescription('homepage') }}">
</head>

<body>
    <h1>{{ $utils->getTitle('homepage') }}</h1>

    <p>{{ $utils->getDescription('homepage') }}</p>

    @if($menu = $utils->getMenu('primary'))
        <ul class="menu">
            @foreach($menu['items'] as $menuItem)
                <li><a href="{{url($menuItem['action'])}}">{{$menuItem['label']}}</a></li>
            @endforeach
        </ul>
    @endif

    <ul>
        @foreach($data['lists'] as $list)
            <li>
                <h2>{{$list['name']}}</h2>
                <p>{{$list['description']}}</p>
                <ul style="display: flex; flex-wrap: wrap">
                    @foreach($list['items'] as $listItem)
                        <figure>
                            <img src="{{$listItem->poster}}" alt="List item poster" width="270px">
                            <figcaption>
                                <a href="{{$utils->getMediaItemUrl($listItem)}}">{{$listItem['name']}}</a>
                            </figcaption>
                        </figure>
                    @endforeach
                </ul>
            </li>
        @endforeach
    </ul>
</body>
</html>
