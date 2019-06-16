<?php

namespace App\Services\Titles\Retrieve;

use App\Episode;
use App\Services\Data\Contracts\DataProvider;
use App\Services\Titles\LoadSeasonData;
use App\Services\Titles\Store\StoreTitleData;
use App\Title;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Arr;

class ShowTitle
{
    /**
     * @param int $id
     * @param array $params
     * @return array
     */
    public function execute($id, $params)
    {
        $title = app(FindOrCreateMediaItem::class)->execute($id, Title::TITLE_TYPE);

        if ($title->needsUpdating() && !Arr::get($params, 'skipUpdating')) {
            $data = app(DataProvider::class)->getTitle($title);
            $title = app(StoreTitleData::class)->execute($title, $data);
        }

        if (isset($params['minimal'])) {
            return $title;
        }

        $videos = Arr::get($params, 'allVideos') ? 'allVideos' : 'videos';
        $title->load(['images', $videos, 'genres', 'seasons' => function(HasMany $query) {
            $query->select(['id', 'number', 'episode_count', 'title_id']);
        }]);

        $this->loadCredits($title, $params);

        if (Arr::get($params, 'keywords')) {
            $title->load('keywords');
        }

        if (Arr::get($params, 'countries')) {
            $title->load('countries');
        }

        if (Arr::get($params, 'seasons')) {
            $title->load('seasons.episodes', 'seasons.credits');
        }

        // load specified season
        if ($seasonNumber = Arr::get($params, 'seasonNumber')) {
            $season = app(LoadSeasonData::class)->execute($title, $seasonNumber);
            $title->setRelation('season', $season);
        }

        // load credits for specified episode
        if (isset($season) && $episodeNumber = (int) Arr::get($params, 'episodeNumber')) {
            $season->episodes->first(function(Episode $episode) use($episodeNumber) {
                return $episodeNumber === $episode->episode_number;
            })->load('credits', 'videos');
        }

        $response = ['title' => $title];

        // load next and last episode to air
        if ($title->is_series && !$title->series_ended) {
            $episodes = $title->getLastAndNextEpisodes();
            if ($episodes && $episodes->count() === 2) {
                $response = array_merge($response, $episodes->toArray());
            }
        }

        return $response;
    }

    private function loadCredits(Title $title, $params)
    {
        $fullCredits = Arr::get($params, 'fullCredits');

        $title->load(['credits' => function(MorphToMany $query) use($fullCredits) {
            // load full credits if needed, based on query params
            if ( ! $fullCredits) {
                $query->wherePivotIn('department', ['cast', 'writing', 'directing', 'creators'])
                    ->groupBy(['name', 'department'])
                    ->limit(50);
            }
        }]);

        if ( ! $fullCredits) {
            $this->filterCredits($title);
        }
    }

    private function filterCredits(Title $title)
    {
        $hasDirector = false;
        $numOfWriters = 0;
        $numOfCast = 0;
        $filteredCredits = $title->credits->filter(function($credit) use(&$numOfCast, &$numOfWriters, &$hasDirector) {
            if ($credit['pivot']['department'] === 'cast' && $numOfCast < 15) {
                $numOfCast++;
                return true;
            }
            if ($credit['pivot']['department'] === 'writing' && $numOfWriters < 3) {
                return true;
            }
            if ($credit['pivot']['job'] === 'director' && !$hasDirector) {
                $hasDirector = true;
                return true;
            }
            if ($credit['pivot']['department'] === 'creators') {
                return true;
            }

            return false;
        })->values();

        $title->setRelation('credits', $filteredCredits);
    }
}