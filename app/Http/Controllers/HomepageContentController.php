<?php

namespace App\Http\Controllers;

use App\ListModel;
use Common\Core\Controller;
use Common\Settings\Settings;

class HomepageContentController extends Controller
{
    /**
     * @var ListModel
     */
    private $list;

    /**
     * @var Settings
     */
    private $settings;

    /**
     * @param ListModel $list
     * @param Settings $settings
     */
    public function __construct(ListModel $list, Settings $settings)
    {
        $this->list = $list;
        $this->settings = $settings;
    }

    public function lists()
    {
        $homepageLists = $this->settings->getJson('homepage.lists');
        if ( ! $homepageLists) return ['lists' => []];

        $lists = $this->list->whereIn('id', $homepageLists)->get();
        $itemCount = $this->settings->get('homepage.list_items_count', 10);
        $sliderItemCount = $this->settings->get('homepage.slider_items_count', 5);

        $lists = $lists->map(function(ListModel $list) use($itemCount, $sliderItemCount, $homepageLists) {
            $list->items = $list->getItems(['limit' => $list->id === $homepageLists[0] ? $sliderItemCount : min($itemCount, 30)]);
            return $list;
        });

        // sort lists by order specified in settings
        $lists = $lists->sortBy(function($model) use($homepageLists) {
            return array_search($model->id, $homepageLists);
        })->values();

        return $this->success(['lists' => $lists]);
    }
}
