<?php

namespace App\Services\Data\Local;

use DB;
use App\Title;
use App\Person;
use App\Episode;
use Carbon\Carbon;
use App\Services\Data\Contracts\DataProvider;
use Illuminate\Support\Arr;

class LocalDataProvider implements DataProvider
{

    public function getTitle(Title $title)
    {
        return [];
    }

    public function getPerson(Person $person)
    {
        return [];
    }

    public function getSeason(Title $title, $seasonNumber)
    {
        return [];
    }

    public function search($query, $params = [])
    {
        $titles = collect();
        $people = collect();

        if (Arr::get($params, 'type') !== 'person') {
            $titles = app(Title::class)
                ->where('name', 'LIKE', "%$query%")
                ->orderBy('popularity', 'desc')
                ->limit(5)
                ->get(['id', 'name', 'year', 'description', 'poster']);
        }

        if (Arr::get($params, 'type') !== 'title') {
            $people = app(Person::class)
                ->with('popularCredits')
                ->where('name', 'LIKE', "%$query%")
                ->orderBy('popularity', 'desc')
                ->limit(5)
                ->get(['id', 'name', 'poster']);
        }

        return $titles
            ->concat($people)
            ->slice(0, Arr::get($params, 'limit', 8))
            ->values();
    }

    public function getTitles($titleType, $titleCategory)
    {
        $titleType = $titleCategory === 'tv' ? 'series' : 'movie';

        if ($titleCategory === 'popular') {
            return app(Title::class)->orderBy('popularity')->limit(20)->where('is_series', $titleType === 'series')->get();
        } else if ($titleCategory === 'topRated') {
            return $this->getTopRatedTitles($titleType);
        } else if ($titleCategory === 'upcoming') {
            return $this->getMoviesReleasingBetween(Carbon::now(), Carbon::now()->addWeek());
        } else if ($titleCategory === 'nowPlaying') {
            return $this->getMoviesReleasingBetween(Carbon::now(), Carbon::now()->subWeek(2));
        } else if ($titleCategory === 'onTheAir') {
           $this->getSeriesAiringBetween(Carbon::now(), Carbon::now()->addWeek());
        } else if ($titleCategory === 'airingToday') {
            return $this->getSeriesAiringBetween(Carbon::now(), Carbon::now()->addDay());
        }
    }
    
    private function getTopRatedTitles($type)
    {
        return app(Title::class)
            ->where('is_series', $type === Title::SERIES_TYPE)
            ->orderBy('local_vote_average', 'desc')
            ->limit(20)
            ->get();
    }

    private function getMoviesReleasingBetween($from, $to)
    {
        return app(Title::class)
            ->whereBetween('release_date', [$from, $to])
            ->orderBy('popularity')
            ->limit(20)
            ->where('is_series', false)
            ->get(['id', 'name']);
    }

    private function getSeriesAiringBetween($from, $to)
    {
        $titleIds = app(Episode::class)
            ->whereBetween('release_date',  [$from, $to])
            ->limit(300)
            ->get(['title_id'])
            ->pluck('title_id')
            ->unique()
            ->slice(0, 20);

        return app(Title::class)->whereIn('id', $titleIds)->get(['id', 'name']);
    }
}