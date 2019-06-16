<?php

Route::group(['prefix' => 'secure'], function () {
    // titles
    Route::get('movies/{id}', 'TitlesController@show');
    Route::get('series/{id}', 'TitlesController@show');
    Route::get('titles/{id}', 'TitlesController@show');
    Route::get('titles/{id}/related', 'RelatedTitlesController@index');
    Route::get('titles', 'TitlesController@index');
    Route::post('titles', 'TitlesController@store');
    Route::post('titles/credits', 'TitleCreditController@store');
    Route::post('titles/credits/reorder', 'TitleCreditController@changeOrder');
    Route::put('titles/credits/{id}', 'TitleCreditController@update');
    Route::delete('titles/credits/{id}', 'TitleCreditController@destroy');
    Route::put('titles/{id}', 'TitlesController@update');
    Route::delete('titles', 'TitlesController@destroy');

    // seasons
    Route::post('titles/{titleId}/seasons', 'SeasonsController@store');
    Route::delete('seasons/{seasonId}', 'SeasonsController@destroy');

    // episodes
    Route::get('episodes/{id}', 'EpisodesController@show');
    Route::post('seasons/{seasonId}/episodes', 'EpisodesController@store');
    Route::put('episodes/{id}', 'EpisodesController@update');
    Route::delete('episodes/{id}', 'EpisodesController@destroy');

    // people
    Route::get('people', 'PeopleController@index');
    Route::get('people/{id}', 'PeopleController@show');
    Route::post('people', 'PeopleController@store');
    Route::put('people/{id}', 'PeopleController@update');
    Route::delete('people', 'PeopleController@destroy');

    // search
    Route::get('search/{query}', 'SearchController@search');

    // update
    Route::get('update', 'UpdateController@show');
    Route::post('update/run', 'UpdateController@update');

    // lists
    Route::get('lists', 'ListsController@index');
    Route::get('lists/{id}', 'ListsController@show');
    Route::post('lists', 'ListsController@store');
    Route::put('lists/{id}', 'ListsController@update');
    Route::post('lists/{id}/reorder', 'ListOrderController@changeOrder');
    Route::delete('lists', 'ListsController@destroy');

    // list items
    Route::post('lists/{id}/add', 'ListItemController@add');
    Route::post('lists/{id}/remove', 'ListItemController@remove');

    // homepage
    Route::get('homepage/lists', 'HomepageContentController@lists');

    // related videos
    Route::get('related-videos', 'RelatedVideosController@index');

    // images
    Route::post('images', 'ImagesController@store');
    Route::delete('images', 'ImagesController@destroy');

    // reviews
    Route::get('reviews', 'ReviewController@index');
    Route::post('reviews', 'ReviewController@store');
    Route::put('reviews/{id}', 'ReviewController@update');
    Route::delete('reviews', 'ReviewController@destroy');

    // news
    Route::get('news', 'NewsController@index');
    Route::get('news/{id}', 'NewsController@show');
    Route::post('news', 'NewsController@store');
    Route::put('news/{id}', 'NewsController@update');
    Route::delete('news', 'NewsController@destroy');

    // videos
    Route::get('videos', 'VideosController@index');
    Route::post('videos', 'VideosController@store');
    Route::put('videos/{id}', 'VideosController@update');
    Route::delete('videos', 'VideosController@destroy');
    Route::post('videos/{id}/rate', 'VideoRatingController@rate');
    Route::post('titles/{id}/videos/change-order', 'VideoOrderController@changeOrder');

    // title tags
    Route::post('titles/{titleId}/tags', 'TitleTagsController@store');
    Route::delete('titles/{titleId}/tags/{type}/{tagId}', 'TitleTagsController@destroy');

    // import
    Route::post('media/import', 'ImportMediaController@importMediaItem');
    Route::get('tmdb/import', 'ImportMediaController@importViaBrowse');
});

$homeController = '\Common\Core\Controllers\HomeController@index';
// FRONT-END ROUTES THAT NEED TO BE PRE-RENDERED
Route::get('/', $homeController)->middleware('prerenderIfCrawler:homepage');
Route::get('browse', $homeController)->middleware('prerenderIfCrawler:browse');
Route::get('titles/{id}', $homeController)->middleware('prerenderIfCrawler:titleShow');
Route::get('titles/{id}/season/{season}/episode/{episode}', $homeController)->middleware('prerenderIfCrawler:episode');
Route::get('titles/{id}/season/{season}', $homeController)->middleware('prerenderIfCrawler:season');
Route::get('people', $homeController)->middleware('prerenderIfCrawler:people');
Route::get('people/{id}', $homeController)->middleware('prerenderIfCrawler:person');
Route::get('news', $homeController)->middleware('prerenderIfCrawler:news-page');
Route::get('news/{id}', $homeController)->middleware('prerenderIfCrawler:news-article');
Route::get('lists/{id}', $homeController)->middleware('prerenderIfCrawler:list');

// CATCH ALL ROUTES AND REDIRECT TO HOME
Route::get('{all}', $homeController)->where('all', '.*');
