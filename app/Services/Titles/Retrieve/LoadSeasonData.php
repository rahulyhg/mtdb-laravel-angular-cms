<?php

namespace App\Services\Titles;

use App\Season;
use App\Services\Data\Contracts\DataProvider;
use App\Services\Data\Tmdb\TmdbApi;
use App\Title;
use App\Episode;
use Carbon\Carbon;
use Common\Settings\Settings;

class LoadSeasonData
{
    /**
     * @var Episode
     */
    private $episode;

    /**
     * @var Season
     */
    private $season;

    /**
     * @var Settings
     */
    private $settings;

    /**
     * @param Episode $episode
     * @param Season $season
     * @param Settings $settings
     */
    public function __construct(Episode $episode, Season $season, Settings $settings)
    {
        $this->episode = $episode;
        $this->season = $season;
        $this->settings = $settings;
    }

    /**
     * @param Title $title
     * @param int $seasonNumber
     * @return Season
     */
    public function execute(Title $title, $seasonNumber)
    {
        $season = $this->findSeason($title->id, $seasonNumber);

        if ($this->needsUpdating($title, $season)) {
            // Fetch title seasons, even if automation is disabled because they can't be
            // fetched when importing multiple titles without hitting tmdb api rate limits
            if ($this->settings->get('content.force_season_update')) {
                $data = app(TmdbApi::class)->getSeason($title, $seasonNumber);
            } else {
                $data = app(DataProvider::class)->getSeason($title, $seasonNumber);
            }
            app(StoreSeasonData::class)->execute($title, $data);
            $season = $this->findSeason($title->id, $seasonNumber);
        }

        $season->load('credits');
        return $season;
    }

    /**
     * @param int $titleId
     * @param int $seasonNumber
     * @return Season
     */
    private function findSeason($titleId, $seasonNumber)
    {
        return $this->season
            ->with('episodes')
            ->where('title_id', $titleId)
            ->where('number', $seasonNumber)
            ->first();
    }

    /**
     * Check if season episodes need updating from external source.
     *
     * @param Title $title
     * @param Season $season
     * @return mixed
     */
    private function needsUpdating(Title $title, Season $season)
    {
        // series ended and this season is already fully updated from external site
        if ($title->series_ended && $season->fully_synced) return false;

        // season is fully synced and it's not the latest season
        if ($season->fully_synced && $title->season_count > $season->number) return false;

        return !$season->updated_at || $season->updated_at->lessThan(Carbon::now()->subWeek());
    }
}