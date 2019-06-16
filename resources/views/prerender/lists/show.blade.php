<!DOCTYPE html>
<html>

@inject('utils', 'App\Services\PrerenderUtils')

<head>
    <meta charset="UTF-8">

    <title>{{ $utils->getTitle('list', 'LIST_NAME', $data['list']['name']) }}</title>

    <meta name="google" content="notranslate">
    <link rel="canonical" href="{{ url('lists', $data['list']['id']) }}">

    <meta itemprop="name" content="{{ $utils->getTitle('list', 'LIST_NAME', $data['list']['name']) }}">
    <meta name="keywords" content="{{$utils->getKeywords('list')}}">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $utils->getTitle('list', 'LIST_NAME', $data['list']['name']) }}">
    <meta name="twitter:url" content="{{ url('lists', $data['list']['id']) }}">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $utils->getTitle('list', 'LIST_NAME', $data['list']['name']) }}">
    <meta property="og:url" content="{{ url('lists', $data['list']['id']) }}">
    <meta property="og:site_name" content="{{ $utils->getSiteName() }}">
    <meta property="og:type" content="website">

    <meta property="og:description" content="{{ $utils->getDescription('list', 'LIST_NAME', $data['list']['name']) }}">
    <meta itemprop="description" content="{{ $utils->getDescription('list', 'LIST_NAME', $data['list']['name']) }}">
    <meta property="description" content="{{ $utils->getDescription('list', 'LIST_NAME', $data['list']['name']) }}">
    <meta name="twitter:description" content="{{ $utils->getDescription('list', 'LIST_NAME', $data['list']['name']) }}">
</head>

<body>
@include('prerender.menu')

<h1>{{ $data['list']['name'] }}</h1>

<ul style="display: flex; flex-wrap: wrap;">
    @foreach($data['items'] as $item)
        <li>
            <figure>
                <img src="{{$utils->getMediaImage($item)}}" alt="List item poster" width="270px">
                <figcaption>
                    <a href="{{$utils->getMediaItemUrl($item)}}">{{$item['name']}}</a>
                </figcaption>
            </figure>
        </li>
    @endforeach
</ul>

</body>
</html>
