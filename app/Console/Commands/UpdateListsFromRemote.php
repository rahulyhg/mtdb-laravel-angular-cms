<?php

namespace App\Console\Commands;

use App\Listable;
use DB;
use App\Title;
use App\ListModel;
use Illuminate\Console\Command;
use App\Services\Lists\AttachListItem;
use App\Services\Data\Contracts\DataProvider;
use App\Services\Titles\Store\StoreTitleData;
use App\Services\Titles\Retrieve\FindOrCreateMediaItem;

class UpdateListsFromRemote extends Command
{
    /**
     * @var string
     */
    protected $signature = 'lists:update';

    /**
     * @var string
     */
    protected $description = 'Update marked lists from selected remote data provider.';

    /**
     * @var DataProvider
     */
    private $dataProvider;

    /**
     * @var ListModel
     */
    private $list;

    /**
     * @param ListModel $list
     * @param DataProvider $dataProvider
     */
    public function __construct(
        ListModel $list,
        DataProvider $dataProvider
    ) {
        parent::__construct();
        $this->dataProvider = $dataProvider;
        $this->list = $list;
    }

    /**
     * @return void
     */
    public function handle()
    {
        $lists = $this->list
            ->whereNotNull('auto_update')
            ->limit(10)
            ->get();

        $lists->each(function(ListModel $list) {
            // movie:upcoming
            list($type, $category) = explode(':', $list->auto_update);
            $titles = $this->dataProvider->getTitles($type, $category);

            // bail if we could not fetch any titles from remote site
            if ( ! $titles || $titles->isEmpty()) return;

            // detach all list items from the list
            app(Listable::class)->where([
                'list_id' => $list->id,
            ])->delete();

            // store fetched titles locally
            $titles->each(function($titleData) use($list) {
                $title = app(FindOrCreateMediaItem::class)->execute($titleData['id'], Title::TITLE_TYPE);
                if ($title->needsUpdating()) {
                    app(StoreTitleData::class)->execute($title, $titleData);
                }
                app(AttachListItem::class)->execute($list, ['itemId' => $title->id, 'itemType' => Title::TITLE_TYPE]);
            });
        });

        $this->info('Lists updated.');
    }
}
