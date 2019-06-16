<?php

namespace App\Http\Controllers;

use App\Services\Titles\GetRelatedTitles;
use App\Title;
use App\Video;
use Common\Core\Controller;
use Illuminate\Http\Request;

class RelatedVideosController extends Controller
{
    /**
     * @var Title
     */
    private $title;

    /**
     * @var Request
     */
    private $request;

    /**
     * RelatedVideosController constructor.
     * @param Request $request
     * @param Title $title
     */
    public function __construct(Request $request, Title $title)
    {
        $this->title = $title;
        $this->request = $request;
    }

    public function index()
    {
        $title = $this->title
            ->with('keywords', 'genres')
            ->findOrFail($this->request->get('titleId'));

        $related = app(GetRelatedTitles::class)->execute($title);
        $videos = [];

        if ($related->isNotEmpty()) {
            $related->load('videos');
            $videos = $related->map(function(Title $title) {
                if ($video = $title->videos->first()) {
                    $video = $title->videos->first();
                    $title->setRelation('videos', []);
                    $video->title = $title;
                    return $video;
                }
            })->filter()->values();
        }

        return $this->success(['videos' => $videos]);
    }
}
