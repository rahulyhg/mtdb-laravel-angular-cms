<?php namespace App\Http\Middleware;

use App;
use App\Http\Controllers\HomepageContentController;
use App\Http\Controllers\ListsController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PeopleController;
use App\Http\Controllers\TitlesController;
use App\Services\PrerenderUtils;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Common\Core\Middleware\PrerenderIfCrawler as BasePrerenderIfCrawler;

class PrerenderIfCrawler extends BasePrerenderIfCrawler
{
    public function __construct(PrerenderUtils $utils)
    {
        parent::__construct($utils);
    }

    protected function getResponse($type, Request $request)
    {
        switch ($type) {
            case 'homepage':
                return $this->prerenderHomepage($request);
            case 'titleShow':
                return $this->prerenderTitle($request);
            case 'season':
                return $this->prerenderSeason($request);
            case 'episode':
                return $this->prerenderEpisode($request);
            case 'person':
                return $this->prerenderPerson($request);
            case 'people':
                return $this->prerenderPeoplePage($request);
            case 'browse':
                return $this->prerenderBrowsePage($request);
            case 'news-article':
                return $this->prerenderNewsArticle($request);
            case 'news-page':
                return $this->prerenderNewsPage($request);
            case 'list':
                return $this->prerenderListPage($request);
        }

        return null;
    }

    /**
     * @param Request $request
     * @return Request|View
     */
    protected function prerenderHomepage(Request $request)
    {
        $payload = $this->getPayload(
            app(HomepageContentController::class)->lists()->getOriginalContent(),
            $request
        );
        return response(view('prerender.homepage')->with($payload));
    }

    protected function prerenderTitle(Request $request)
    {
        $payload = $this->getPayload(
            app(TitlesController::class)->show($request->route('id'))->getOriginalContent(),
            $request
        );
        return response(view('prerender.titles.show')->with($payload));
    }

    private function prerenderPerson(Request $request)
    {
        $payload = $this->getPayload(
            app(PeopleController::class)->show($request->route('id'))->getOriginalContent(),
            $request
        );
        return response(view('prerender.people.show')->with($payload));
    }

    private function prerenderBrowsePage(Request $request)
    {
        $payload = $this->getPayload(
            app(TitlesController::class)->index($request->route('id')),
            $request
        );
        $payload['data']->withPath('browse');
        $payload['data']->appends(['_escaped_fragment_' => 1]);
        return response(view('prerender.titles.index')->with($payload));
    }

    private function getPayload($data, Request $request)
    {
        return $payload = [
            'name' => urldecode($request->route('name')),
            'data' => $data,
        ];
    }

    private function prerenderNewsArticle(Request $request)
    {
        $payload = $this->getPayload(
            app(NewsController::class)->show($request->route('id'))->getOriginalContent(),
            $request
        );
        return response(view('prerender.news.show')->with($payload));
    }

    private function prerenderNewsPage(Request $request)
    {
        $payload = $this->getPayload(
            app(NewsController::class)->index(),
            $request
        );
        $payload['data']->withPath('news');
        $payload['data']->appends(['_escaped_fragment_' => 1]);
        return response(view('prerender.news.index')->with($payload));
    }

    private function prerenderPeoplePage(Request $request)
    {
        $payload = $this->getPayload(
            app(PeopleController::class)->index(),
            $request
        );
        $payload['data']->withPath('people');
        $payload['data']->appends(['_escaped_fragment_' => 1]);
        return response(view('prerender.people.index')->with($payload));
    }

    private function prerenderListPage(Request $request)
    {
        $payload = $this->getPayload(
            app(ListsController::class)->show($request->route('id'))->getOriginalContent(),
            $request
        );
        return response(view('prerender.lists.show')->with($payload));
    }

    private function prerenderSeason(Request $request)
    {
        $request->merge(['seasonNumber' => $request->route('season')]);

        $payload = $this->getPayload(
            app(TitlesController::class)->show($request->route('id'))->getOriginalContent(),
            $request
        );
        return response(view('prerender.seasons.show')->with($payload));
    }

    private function prerenderEpisode(Request $request)
    {
        $request->merge([
            'seasonNumber' => $request->route('season'),
            'episodeNumber' => $request->route('episode'),
        ]);

        $payload = $this->getPayload(
            app(TitlesController::class)->show($request->route('id'))->getOriginalContent(),
            $request
        );

        $payload['data']['episode'] = array_first($payload['data']['title']['season']['episodes'], function($episode) use($request) {
            return $episode['episode_number'] === (int) $request->route('episode');
        });

        return response(view('prerender.episodes.show')->with($payload));
    }
}