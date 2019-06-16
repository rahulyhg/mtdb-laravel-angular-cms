<?php

namespace App\Services\Titles\Retrieve;

use App\Title;
use Common\Database\Paginator;
use Common\Settings\Settings;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class PaginateTitles
{
    /**
     * @var Title
     */
    private $title;

    /**
     * @var Settings
     */
    private $settings;

    /**
     * @param Title $title
     * @param Settings $settings
     */
    public function __construct(Title $title, Settings $settings)
    {
        $this->title = $title;
        $this->settings = $settings;
    }

    /**
     * @param array $params
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function execute($params)
    {
        $paginator = new Paginator($this->title);
        $paginator->where('adult', false);
        $paginator->setDefaultOrderColumns('popularity', 'desc');

        if ($type = Arr::get($params, 'type')) {
            $paginator->where('is_series', $type === Title::SERIES_TYPE);
        }

        if ($genre = Arr::get($params, 'genre')) {
            $genres = explode(',', $genre);
            $paginator->query()->whereHas('genres', function(Builder $query) use($genres) {
                $genres = array_map(function($genre) {
                    return Str::slug($genre);
                }, $genres);
                $query->whereIn('name', $genres);
            });
        }

        if ($released = Arr::get($params, 'released')) {
            $this->byReleaseDate($released, $paginator);
        }

        if ($runtime = Arr::get($params, 'runtime')) {
            $this->byRuntime($runtime, $paginator);
        }

        if ($score = Arr::get($params, 'score')) {
            $this->byRating($score, $paginator);
        }

        if ($language = Arr::get($params, 'language')) {
            $paginator->query()->where('language', $language);
        }

        if ($certification = Arr::get($params, 'certification')) {
            $paginator->query()->where('certification', $certification);
        }

        if ($country = Arr::get($params, 'country')) {
            $paginator->query()->whereHas('countries', function(Builder $query) use($country) {
                $query->where('name', $country);
            });
        }

        if ($onlyStreamable = Arr::get($params, 'onlyStreamable')) {
            $paginator->query()->whereHas('allVideos', function(Builder $query) use($country) {
                $query->where('source', 'local');
            });
        }

        if ($order = Arr::get($params, 'order')) {
            $order = str_replace('date_added', 'created_at', $order);
            $order = str_replace('user_score', config('common.site.rating_column'), $order);

            // show titles with less then 50 votes on tmdb last, regardless of their average
            if (str_contains($order, 'tmdb_vote_average')) {
                $paginator->query()->orderBy(DB::raw('tmdb_vote_count > 100'), 'desc');
            }

            $params['order'] = $order;
        }

        if ( ! isset($params['perPage'])) {
            $params['perPage'] = 16;
        }

        return $paginator->paginate($params);
    }

    private function byRuntime($runtimes, Paginator $paginator)
    {
        $parts = explode(',', $runtimes);
        if (count($parts) !== 2) return;

        $paginator->query()
            ->where('runtime', '>=', $parts[0])
            ->where('runtime', '<=', $parts[1]);
    }

    private function byReleaseDate($dates, Paginator $paginator)
    {
        $parts = explode(',', $dates);
        if (count($parts) !== 2) return;

        $paginator->query()
            ->where('release_date', '>=', $parts[0])
            ->where('release_date', '<=', $parts[1]);
    }

    private function byRating($scores, Paginator $paginator)
    {
        $parts = explode(',', $scores);
        if (count($parts) !== 2) return;

        if ($this->settings->get('content.automation')) {
            $paginator->query()
                ->where('tmdb_vote_average', '>=', $parts[0])
                ->where('tmdb_vote_average', '<=', $parts[1])
                ->where('tmdb_vote_count', '>=', 50);
        } else {
            $paginator->query()
                ->where('local_vote_average', '>=', $parts[0])
                ->where('local_vote_average', '<=', $parts[1]);
        }
    }
}