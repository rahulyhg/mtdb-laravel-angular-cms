<!DOCTYPE html>
<html>

@inject('utils', 'App\Services\PrerenderUtils')

<head>
    <meta charset="UTF-8">

    <title>{{ $utils->getTitle('person', 'PERSON_NAME', $data['person']['name']) }}</title>

    <meta name="google" content="notranslate">
    <link rel="canonical" href="{{ $utils->getMediaItemUrl($data['person']) }}">

    <meta itemprop="name" content="{{ $utils->getTitle('person', 'PERSON_NAME', $data['person']['name']) }}">
    <meta name="keywords" content="{{$utils->getKeywords('person')}}">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $utils->getTitle('person', 'PERSON_NAME', $data['person']['name']) }}">
    <meta name="twitter:url" content="{{ $utils->getMediaItemUrl($data['person']) }}">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $utils->getTitle('person', 'PERSON_NAME', $data['person']['name']) }}">
    <meta property="og:url" content="{{ $utils->getMediaItemUrl($data['person']) }}">
    <meta property="og:site_name" content="{{ $utils->getSiteName() }}">
    <meta property="og:type" content="website">

    <meta property="og:description" content="{{ $utils->getDescription('person', 'person_description', $data['person']['description']) }}">
    <meta itemprop="description" content="{{ $utils->getDescription('person', 'person_description', $data['person']['description']) }}">
    <meta property="description" content="{{ $utils->getDescription('person', 'person_description', $data['person']['description']) }}">
    <meta name="twitter:description" content="{{ $utils->getDescription('person', 'person_description', $data['person']['description']) }}">
</head>

<body>
@if($menu = $utils->getMenu('primary'))
    <ul class="menu">
        @foreach($menu['items'] as $menuItem)
            <li><a href="{{url($menuItem['action'])}}">{{$menuItem['label']}}</a></li>
        @endforeach
    </ul>
@endif

<h1>{{ $data['person']['name'] }}</h1>

<img src="{{$utils->getMediaImage($data['person'])}}" alt="Person poster" width="270px">

<div>
    <h3>{{__('Biography')}}</h3>
    <p>{{$data['person']['description']}}</p>
</div>

<div>
    <h3>{{__('Personal Facts')}}</h3>
    <dl>
        <dt>{{__('Known For')}}</dt>
        <dd>{{$data['person']['known_for']}}</dd>

        <dt>{{__('Gender')}}</dt>
        <dd>{{$data['person']['gender']}}</dd>

        <dt>{{__('Known Credits')}}</dt>
        <dd>{{count($data['person']['credits'])}}</dd>

        @if($data['person']['birth_date'])
            <dt>{{__('Birth Date')}}</dt>
            <dd>{{$data['person']['birth_date']}}</dd>
        @endif

        @if($data['person']['birth_place'])
            <dt>{{__('Birth Place')}}</dt>
            <dd>{{$data['person']['birth_place']}}</dd>
        @endif
    </dl>
</div>

<div>
    <h3>{{__('Known For')}}</h3>
    <ul style="display: flex; flex-wrap: wrap;">
        @foreach($data['knownFor'] as $credit)
            <li>
                <figure>
                    <img src="{{$utils->getMediaImage($credit)}}" alt="Credit poster" width="270px">
                    <figcaption>
                        <a href="{{$utils->getMediaItemUrl($credit)}}">{{$credit['name']}}</a>
                    </figcaption>
                </figure>
            </li>
        @endforeach
    </ul>
</div>

<div>
    <h3>{{__('Credits')}}</h3>
    <ul>
        @foreach($data['credits'] as $groupName => $creditGroup)
            <li style="margin-bottom: 15px;">
                <h4 style="text-transform: capitalize">{{$groupName}} ({{count($creditGroup)}} credits)</h4>
                <ul>
                    @foreach($creditGroup as $credit)
                        <li style="margin-bottom: 15px;">
                            <div class="meta">
                                <a href="{{$utils->getMediaItemUrl($credit)}}">{{$credit['name']}}</a>
                                <div>{{$credit['pivot']['character']}}</div>
                                <div>{{$credit['pivot']['job']}}</div>
                                <div>{{$credit['pivot']['department']}}</div>

                                @if(isset($credit['episodes']))
                                    <div class="episode-list">
                                        @foreach($credits->episodes as $episodeCredit)
                                            <div class="episode-credit">
                                                <div class="episode-name">
                                                    <span>- </span>
                                                    <a href="{{$utils->getMediaItemUrl($episodeCredit)}}">{{$episodeCredit['name']}}</a>
                                                    <span> ({{$episodeCredit['year']}})</span>
                                                    <span class="episode-separator"> ... </span>
                                                    <span>
                                                <span>{{$episodeCredit['pivot']['character']}}</span>
                                                <span>{{$episodeCredit['pivot']['job']}}</span>
                                                    <span>{{$episodeCredit['pivot']['department']}}</span>
                                            </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="year">{{$credit['year']}}</div>
                        </li>
                    @endforeach
                </ul>
            </li>
        @endforeach
    </ul>
</div>
</body>
</html>
