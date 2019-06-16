<?php

namespace App\Services\Data\Tmdb;

use App\Person;
use App\Services\Data\Contracts\DataProvider;
use App\Title;
use Common\Settings\Settings;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Log;

class TmdbApi implements DataProvider
{
    const TMDB_BASE = 'https://api.themoviedb.org/3/';
    const DEFAULT_TMDB_LANGUAGE = 'en';

    protected $includeAdult;
    protected $language;

    /**
     * @var Client
     */
    protected $http;

    /**
     * @var Settings
     */
    private $settings;

    /**
     * @param Client $http
     * @param Settings $settings
     */
    public function __construct(Client $http, Settings $settings)
    {
        $this->http = $http;
        $this->settings = $settings;

        $this->language = $this->settings->get('tmdb.language', self::DEFAULT_TMDB_LANGUAGE);
        $this->includeAdult = $this->settings->get('tmdb.includeAdult', false);
    }

    public function getPerson(Person $person)
    {
        $appends = ['combined_credits', 'images', 'tagged_images'];

        $response = $this->call(
            "person/{$person->tmdb_id}",
            ['append_to_response' => implode(',', $appends)]
        );

        return app(TransformData::class)->execute([$response])->first();
    }

    public function getSeason(Title $title, $seasonNumber)
    {
        if ( ! $title->tmdb_id) return [];

        $response = $this->call(
            "tv/{$title->tmdb_id}/season/{$seasonNumber}",
            ['append_to_response' => 'credits']
        );

        $data = app(TransformData::class)->execute([$response])->first();
        $data['fully_synced'] = true;

        return $data;
    }

    public function getTitle(Title $title)
    {
        if ( ! $title->tmdb_id) return [];

        $appends = [
            'credits', 'external_ids', 'images', 'content_ratings',
            'keywords', 'release_dates', 'videos', 'seasons'
        ];

        $uri = $title->is_series ? 'tv' : 'movie';

        $response = $this->call(
            "$uri/{$title->tmdb_id}",
            ['append_to_response' => implode(',', $appends)]
        );

        $data = app(TransformData::class)->execute([$response])->first();
        $data['fully_synced'] = true;
        return $data;
    }

    /**
     * @param $query
     * @param array $params
     * @return \Illuminate\Support\Collection
     */
    public function search($query, $params = [])
    {
        $response = $this->call('search/multi', ['query' => $query]);
        $results = app(TransformData::class)->execute($response['results']);

        $type = Arr::get($params, 'type');
        $limit = Arr::get($params, 'limit', 8);

        if ($type) {
            $results = $results->filter(function($result) use($type) {
                return $result['type'] === $type;
            });
        }

        return $results
            ->sortByDesc('popularity')
            ->slice(0, $limit)
            ->values();
    }

    public function getTitles($titleType, $titleCategory)
    {
        $titleCategory = snake_case($titleCategory);
        $uri = $titleType . '/' . $titleCategory;

        $validUris = [
            'movie/popular',
            'movie/top_rated',
            'movie/upcoming',
            'movie/now_playing',
            'tv/popular',
            'tv/top_rated',
            'tv/on_the_air',
            'tv/airing_today',
        ];

        if (array_search($uri, $validUris) === false) {
            Log::error("Trying to fetch titles from '$uri', but this uri is not valid.");
            return collect();
        }

        $response = $this->call($uri);
        return app(TransformData::class)->execute($response['results']);
    }

    public function browse($page = 1, $type = 'movie', $queryParams = [])
    {
        if ($page > 1000) {
            throw new Exception('Maximum page is 1000');
        }

        $apiParams = array_merge(
            $queryParams,
            ['sort_by' => 'popularity.desc', 'page' => $page]
        );

        $response = $this->call("discover/$type", $apiParams);

        $response['results'] = app(TransformData::class)->execute($response['results']);
        return $response;
    }

    /**
     * @param string $uri
     * @param array $queryParams
     * @return array
     */
    protected function call($uri, $queryParams = [])
    {
        $key = config('services.tmdb.key');
        $url = self::TMDB_BASE . "$uri?api_key=$key";

        $queryParams = array_merge($queryParams, [
            'include_adult' => $this->includeAdult,
            'language' => $this->language,
            'region' => 'US',
            'include_image_language' => 'en,null'
        ]);
        $url .= '&' . urldecode(http_build_query($queryParams));

        $response = $this->http->get($url, [
            'verify' => false,
        ])->getBody()->getContents();

        return json_decode($response, true);
    }
}