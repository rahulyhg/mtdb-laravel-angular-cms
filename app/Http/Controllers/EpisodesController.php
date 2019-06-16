<?php

namespace App\Http\Controllers;

use App\Episode;
use App\Season;
use Common\Core\Controller;
use Illuminate\Http\Request;

class EpisodesController extends Controller
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Episode
     */
    private $episode;

    /**
     * @var Season
     */
    private $season;

    /**
     * @param Request $request
     * @param Episode $episode
     * @param Season $season
     */
    public function __construct(
        Request $request,
        Episode $episode,
        Season $season
    )
    {
        $this->request = $request;
        $this->episode = $episode;
        $this->season = $season;
    }

    public function show($id)
    {
        $this->authorize('show', Episode::class);

        $episode = $this->episode->with('credits')->findOrFail($id);

        return $this->success(['episode' => $episode]);
    }

    public function update($id)
    {
        $this->authorize('update', Episode::class);

        $episode = $this->episode->findOrFail($id);
        $episode->fill($this->request->all())->save();

        return $this->success(['episode' => $episode]);
    }

    public function store($seasonId)
    {
        $this->authorize('store', Episode::class);

        $season = $this->season->withCount('episodes')->findOrFail($seasonId);
        $episodeCount = $season->episodes_count + 1;

        $episode = $this->episode->create(array_merge(
            $this->request->all(),
            [
                'season_number' => $season->number,
                'episode_number' => $episodeCount,
                'season_id' => $season->id,
                'title_id' => $season->title_id,
            ]
        ));

        // increment episode_count on season
        $season->fill(['episode_count' => $episodeCount])->save();

        // increment episode_count on title
        $season->title->fill(['episode_count' => $episodeCount])->save();

        return $this->success(['episode' => $episode]);
    }

    public function destroy($id)
    {
        $this->authorize('destroy', Episode::class);

        $episode = $this->episode->findOrFail($id);
        $episode->credits()->detach();
        // TODO: delete episode poster image
        $episode->delete();

        $episode->season()->decrement('episode_count');
        $episode->title()->decrement('episode_count');

        return $this->success();
    }
}
