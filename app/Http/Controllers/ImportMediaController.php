<?php

namespace App\Http\Controllers;

use App\Person;
use App\Services\Data\Tmdb\TmdbApi;
use App\Services\People\Store\StorePersonData;
use App\Services\Titles\Retrieve\FindOrCreateMediaItem;
use App\Services\Titles\Store\StoreTitleData;
use App\Services\Traits\HandlesTitleId;
use App\Title;
use Common\Core\Controller;
use Illuminate\Http\Request;

class ImportMediaController extends Controller
{
    use HandlesTitleId;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function importMediaItem()
    {
        $this->authorize('store', Title::class);

        $this->validate($this->request, [
            'mediaType' => 'required|string',
            'tmdbId' => 'required|integer',
        ]);

        $mediaType = $this->request->get('mediaType');
        $tmdbId = $this->request->get('tmdbId');

        $encodedId = $this->encodeId('tmdb', $mediaType, $tmdbId);
        $mediaItem = app(FindOrCreateMediaItem::class)->execute($encodedId, $mediaType);

        if ($mediaItem->needsUpdating(true)) {
            $mediaItem = $mediaItem->type === Person::PERSON_TYPE ?
                $this->updatePerson($mediaItem) :
                $this->updateTitle($mediaItem);
        }

        return ['mediaItem' => $mediaItem];
    }

    public function importViaBrowse()
    {
        $this->authorize('store', Title::class);

        @set_time_limit(0);

        $type = $this->request->get('type', 'movie');
        $limit = $this->request->get('limit', 1000);
        $page = $this->request->get('page', 1);

        $tmdbParams = $this->request->except(['type', 'limit', 'page']);

        // if page is more then 1, need to increase limit as well
        $limit = min(1000, $limit + $page);

        for ($i = $page; $i <= $limit; $i++) {
            $response = app(TmdbApi::class)->browse($i, $type, $tmdbParams);
            $response['results']->each(function($result, $index) use($i) {
                $title = app(FindOrCreateMediaItem::class)->execute($result['id'], Title::TITLE_TYPE);
                if ($title->needsUpdating(true)) {
                    $this->updateTitle($title);
                }
                echo "Imported page: $i | title: $index | {$title->name} <br>";
                $this->flushOutput();
            });
        }

        return 'Done Importing.';
    }

    private function updateTitle(Title $title)
    {
        try {
            $data = app(TmdbApi::class)->getTitle($title);
        } catch (\Exception $e) {
            return null;
        }

        return app(StoreTitleData::class)->execute($title, $data);
    }

    private function updatePerson(Person $person)
    {
        $data = app(TmdbApi::class)->getPerson($person);
        return app(StorePersonData::class)->execute($person, $data);
    }

    private function flushOutput()
    {
        flush();
        $levels = ob_get_level();
        for ($i=0; $i<$levels; $i++) {
            ob_end_flush();
        }
    }
}
