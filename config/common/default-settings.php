<?php

return [
    // branding
    ['name' => 'branding.site_description', 'value' => "MTDb, the world's most popular and authoritative source for movie, TV and celebrity content."],

    // other
    ['name' => 'site.force_https', 'value' => 0],

    // menus
    ['name' => 'menus', 'value' => json_encode([
        ['name' => 'Primary', 'position' => 'primary', 'items' => [
            ['type' => 'route', 'order' => 1, 'label' => 'Movies', 'action' => 'browse?type=movie'],
            ['type' => 'route', 'order' => 2, 'label' => 'Series', 'action' => 'browse?type=series'],
            ['type' => 'route', 'order' => 3, 'label' => 'People', 'action' => 'people'],
            ['type' => 'route', 'order' => 4, 'label' => 'News', 'action' => 'news']
        ]],

        ['name' => 'Explore', 'position' => 'footer-1', 'items' => [
            ['type' => 'route', 'order' => 1, 'label' => 'Top Movies', 'action' => 'lists/1'],
            ['type' => 'route', 'order' => 2, 'label' => 'Top Shows', 'action' => 'lists/2'],
            ['type' => 'route', 'order' => 3, 'label' => 'Coming Soon', 'action' => 'lists/3'],
            ['type' => 'route', 'order' => 4, 'label' => 'Now Playing', 'action' => 'lists/4'],
            ['type' => 'route', 'order' => 3, 'label' => 'People', 'action' => 'people'],
        ]],

        ['name' => 'Genres', 'position' => 'footer-2', 'items' => [
            ['type' => 'route', 'order' => 1, 'label' => 'Action', 'action' => 'browse?genre=action'],
            ['type' => 'route', 'order' => 2, 'label' => 'Comedy', 'action' => 'browse?genre=comedy'],
            ['type' => 'route', 'order' => 2, 'label' => 'Drama', 'action' => 'browse?genre=drama'],
            ['type' => 'route', 'order' => 2, 'label' => 'Crime', 'action' => 'browse?genre=crime'],
            ['type' => 'route', 'order' => 2, 'label' => 'Adventure', 'action' => 'browse?genre=adventure'],
        ]],

        ['name' => 'Pages', 'position' => 'footer-3', 'items' => [
            ['type' => 'route', 'order' => 1, 'label' => 'Contact', 'action' => 'contact'],
            ['type' => 'page', 'order' => 2, 'label' => 'Privacy Policy', 'action' => '1/privacy-policy'],
            ['type' => 'page', 'order' => 2, 'label' => 'Terms of Use', 'action' => '2/terms-of-use'],
            ['type' => 'page', 'order' => 2, 'label' => 'About Us', 'action' => '3/about-us'],
        ]],
    ])],

    // seo
    ['name' => 'seo.homepage_title', 'value' => 'MTDb - Movies, TV and Celebrities'],
    ['name' => 'seo.homepage_description', 'value' => 'The Movie Database (MTDb) is a popular database for movies, TV shows and celebrities.'],
    ['name' => 'seo.homepage_tags', 'value' => "movies, films, movie database, actors, actresses, directors, stars, synopsis, trailers, credits, cast"],

    // titles - show
    ['name' => 'seo.movie_title', 'value' => '{{MOVIE_NAME}} ({{MOVIE_YEAR}}) - MTDb'],
    ['name' => 'seo.movie_description', 'value' => '{{MOVIE_DESCRIPTION}}'],
    ['name' => 'seo.movie_tags', 'value' => "reviews,photos,user ratings,synopsis,trailers,credits"],

    // titles - index
    ['name' => 'seo.browse_title', 'value' => 'Browse - MTDb'],
    ['name' => 'seo.browse_description', 'value' => 'Browse movies and series based on specified filters.'],
    ['name' => 'seo.browse_tags', 'value' => "movies, tv, browse, filters, search"],

    // seasons - show
    ['name' => 'seo.season_title', 'value' => '{{SERIES_NAME}} ({{SERIES_YEAR}}) - Season {{SEASON_NUMBER}} - MTDb'],
    ['name' => 'seo.season_description', 'value' => 'List of episodes for {{SERIES_NAME}}: Season {{SEASON_NUMBER}}'],
    ['name' => 'seo.season_tags', 'value' => "reviews,photos,user ratings,synopsis,trailers,credits"],

    // episodes - show
    ['name' => 'seo.episode_title', 'value' => '{{SERIES_NAME}} ({{SERIES_YEAR}}) - {{EPISODE_NAME}} - MTDb'],
    ['name' => 'seo.episode_description', 'value' => '{{EPISODE_DESCRIPTION}}'],
    ['name' => 'seo.episode_tags', 'value' => "reviews,photos,user ratings,synopsis,trailers,credits"],

    // people - show
    ['name' => 'seo.person_title', 'value' => '{{PERSON_NAME}} - MTDb'],
    ['name' => 'seo.person_description', 'value' => '{{PERSON_DESCRIPTION}}'],
    ['name' => 'seo.person_tags', 'value' => "biography, facts, photos, credits"],

    // people - index
    ['name' => 'seo.people_title', 'value' => 'Popular People - MTDb'],
    ['name' => 'seo.people_description', 'value' => 'The Movie Database (MTDb) is a popular database for movies, TV shows and celebrities.'],
    ['name' => 'seo.people_tags', 'value' => "movies, films, movie database, actors, actresses, directors, stars, synopsis, trailers, credits, cast"],

    // news - show
    ['name' => 'seo.news_article_title', 'value' => '{{ARTICLE_TITLE}} - MTDb'],
    ['name' => 'seo.news_article_description', 'value' => 'The Movie Database (MTDb) is a popular database for movies, TV shows and celebrities.'],
    ['name' => 'seo.news_article_tags', 'value' => "movies, films, movie database, actors, actresses, directors, stars, synopsis, trailers, credits, cast"],

    // news - index
    ['name' => 'seo.news_page_title', 'value' => 'Latest news - MTDb'],
    ['name' => 'seo.news_page_description', 'value' => 'The Movie Database (MTDb) is a popular database for movies, TV shows and celebrities.'],
    ['name' => 'seo.news_page_tags', 'value' => "movies, films, movie database, actors, actresses, directors, stars, synopsis, trailers, credits, cast"],

    // list - show
    ['name' => 'seo.list_title', 'value' => '{{LIST_NAME}} - MTDb'],
    ['name' => 'seo.list_description', 'value' => '{{LIST_NAME}}'],
    ['name' => 'seo.list_tags', 'value' => "movies, films, movie database, actors, actresses, directors, stars, synopsis, trailers, credits, cast"],

    // uploads
    ['name' => 'uploads.max_size', 'value' => 52428800],
    ['name' => 'uploads.available_space', 'value' => 104857600],
    ['name' => 'uploads.blocked_extensions', 'value' => json_encode(['exe', 'application/x-msdownload', 'x-dosexec'])],

    // content
    ['name' => 'news.auto_update', 'value' => 0],
    ['name' => 'content.automation', 'value' => 0],
    ['name' => 'tmdb.language', 'value' => 'en'],
    ['name' => 'titles.show_videos_panel', 'value' => 0],
    ['name' => 'titles.enable_reviews', 'value' => 1],
    ['name' => 'homepage.list_items_count', 'value' => 10],
    ['name' => 'homepage.lists', 'value' => json_encode([1, 2, 3, 4])],
    ['name' => 'browse.genres', 'value' => json_encode([
        'drama', 'action', 'thriller', 'comedy',
        'science fiction', 'horror', 'mystery', 'romance',
        ])
    ],
];
