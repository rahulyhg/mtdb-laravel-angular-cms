<?php

namespace App\Http\Controllers;

use App\Episode;
use App\Services\Videos\CrupdateVideo;
use App\Video;
use Common\Core\Controller;
use Common\Database\Paginator;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class VideosController extends Controller
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Video
     */
    private $video;

    /**
     * @var Episode
     */
    private $episode;

    /**
     * @param Request $request
     * @param Video $video
     * @param Episode $episode
     */
    public function __construct(Request $request, Video $video, Episode $episode)
    {
        $this->request = $request;
        $this->video = $video;
        $this->episode = $episode;
    }

    public function index()
    {
        $this->authorize('index', Video::class);

        $paginator = (new Paginator($this->video));
        $paginator->with(['title' => function(BelongsTo $query) {
            $query->select('id', 'name', 'backdrop', 'is_series', 'season_count');
        }]);

        if ($titleId = $this->request->get('titleId')) {
            $paginator->where('title_id', $titleId);
        }

        if ($source = $this->request->get('source')) {
            $paginator->where('source', $source);
        }

        $paginator = $paginator->paginate($this->request->all());

        return $paginator;
    }

    public function store()
    {
        $this->authorize('store', Video::class);

        $this->validate($this->request, [
            'name' => 'required|string|min:3|max:250',
            'url' => 'required|max:250', // TODO: can't use "url" in PHP 7.3 until laravel upgrade
            'type' => 'required|string|min:3|max:250',
            'quality' => 'required|string|min:2|max:250',
            'title_id' => 'required|integer',
            'season' => 'nullable|integer',
            'episode' => 'requiredWith:season|integer|nullable',
        ]);

        $video = app(CrupdateVideo::class)->execute($this->request->all());

        return $this->success(['video' => $video]);
    }

    public function update($id)
    {
        $this->authorize('update', Video::class);

        $this->validate($this->request, [
            'name' => 'string|min:3|max:250',
            'url' => 'max:250', // TODO: can't use "url" in PHP 7.3 until laravel upgrade
            'type' => 'string|min:3|max:250',
            'quality' => 'string|min:2|max:250',
            'title_id' => 'integer',
            'season' => 'nullable|integer',
            'episode' => 'requiredWith:season|integer|nullable',
        ]);

        $video = app(CrupdateVideo::class)->execute($this->request->all(), $id);

        return $this->success(['video' => $video]);
    }

    public function destroy()
    {
        $this->authorize('destroy', Video::class);

        $this->video->whereIn('id', $this->request->get('ids'))->delete();

        return $this->success();
    }
}
