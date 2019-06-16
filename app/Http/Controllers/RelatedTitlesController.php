<?php

namespace App\Http\Controllers;

use App\Services\Titles\GetRelatedTitles;
use App\Title;
use App\Video;
use Common\Core\Controller;
use Illuminate\Http\Request;

class RelatedTitlesController extends Controller
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

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($id)
    {
        $this->authorize('index', Title::class);

        $title = $this->title
            ->with('keywords', 'genres')
            ->findOrFail($id);

        $related = app(GetRelatedTitles::class)
            ->execute($title, $this->request->all());

        return $this->success(['titles' => $related]);
    }
}
