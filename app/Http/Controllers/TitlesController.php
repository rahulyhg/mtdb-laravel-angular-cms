<?php

namespace App\Http\Controllers;

use App\Episode;
use App\Image;
use App\Jobs\IncrementModelViews;
use App\Listable;
use App\Review;
use App\Season;
use App\Services\Titles\Retrieve\PaginateTitles;
use App\Services\Titles\Retrieve\ShowTitle;
use App\Services\Titles\Store\StoreTitleData;
use App\Title;
use App\Video;
use Common\Core\Controller;
use DB;
use Illuminate\Http\Request;

class TitlesController extends Controller
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Title
     */
    private $title;

    /**
     * @param Request $request
     * @param Title $title
     */
    public function __construct(Request $request, Title $title)
    {
        $this->request = $request;
        $this->title = $title;
    }

    /**
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function index()
    {
        $this->authorize('index', Title::class);

        return app(PaginateTitles::class)->execute($this->request->all());
    }

    /**
     * @param string|integer $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $this->authorize('show', Title::class);

        $response = app(ShowTitle::class)->execute($id, $this->request->all());

        $this->dispatch(new IncrementModelViews($response['title']));

        return $this->success($response);
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id)
    {
        $this->authorize('update', Title::class);

        $data = $this->request->all();
        $title = $this->title->findOrFail($id);

        $title = app(StoreTitleData::class)->execute($title, $data);

        return $this->success(['title' => $title]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function store()
    {
        $this->authorize('store', Title::class);

        $title = $this->title->create($this->request->all());

        return $this->success(['title' => $title]);
    }

    public function destroy()
    {
        $this->authorize('destroy', Title::class);

        $titleIds = $this->request->get('ids');

        // seasons
        app(Season::class)->whereIn('title_id', $titleIds)->delete();

        // episodes
        $episodeIds = app(Episode::class)->whereIn('title_id', $titleIds)->pluck('id');
        app(Episode::class)->whereIn('id', $episodeIds)->delete();

        // images
        app(Image::class)
            ->whereIn('model_id', $titleIds)
            ->where('model_type', Title::class)
            ->delete();

        // list items
        app(Listable::class)
            ->whereIn('listable_id', $titleIds)
            ->where('listable_type', Title::class)
            ->delete();

        // reviews
        app(Review::class)
            ->whereIn('reviewable_id', $titleIds)
            ->where('reviewable_id', Title::class)
            ->delete();

        app(Review::class)
            ->whereIn('reviewable_id', $episodeIds)
            ->where('reviewable_id', Episode::class)
            ->delete();

        // tags
        DB::table('taggables')
            ->whereIn('taggable_id', $titleIds)
            ->where('taggable_type', Title::class)
            ->delete();

        // videos
        $videoIds = app(Video::class)
            ->whereIn('title_id', $titleIds)
            ->pluck('id');
        app(Video::class)->whereIn('id', $videoIds)->delete();

        DB::table('video_ratings')->whereIn('video_id', $videoIds)->delete();

        // titles
        $this->title->whereIn('id', $titleIds)->delete();

        return $this->success();
    }
}
