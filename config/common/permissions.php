<?php

return [
    'roles' => [
        'users' => [
            'titles.view' => 1,
            'people.view' => 1,
            'reviews.view' => 1,
            'reviews.create' => 1,
            'news.view' => 1,
            'lists.create' => 1,
        ],
        'guests' => [
            'titles.view' => 1,
            'people.view' => 1,
            'reviews.view' => 1,
            'news.view' => 1,
        ]
    ],
    'all' => [
        'titles' => [
            'titles.view',
            'titles.create',
            'titles.update',
            'titles.delete',
        ],
        'reviews' => [
            'reviews.view',
            'reviews.create',
            'reviews.update',
            'reviews.delete',
        ],
        'people' => [
            'people.view',
            'people.create',
            'people.update',
            'people.delete',
        ],
        'news' => [
            'news.view',
            'news.create',
            'news.update',
            'news.delete',
        ],
        'videos' => [
            'videos.rate',
            'videos.view',
            'videos.create',
            'videos.update',
            'videos.delete',
        ],
        'lists' => [
            'lists.rate',
            'lists.view',
            'lists.create',
            'lists.update',
            'lists.delete',
        ],
    ]
];
