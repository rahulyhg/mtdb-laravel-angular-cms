<?php namespace App\Services;

use App\ListModel;

class AppBootstrapData
{
    /**
     * @var ListModel
     */
    private $list;

    /**
     * @param ListModel $list
     */
    public function __construct(ListModel $list)
    {
        $this->list = $list;
    }

    /**
     * Get data needed to bootstrap the application.
     *
     * @param $bootstrapData
     * @return array
     */
    public function get($bootstrapData)
    {
        if ( ! isset($bootstrapData['user'])) return $bootstrapData;

        $bootstrapData = $this->getWatchlist($bootstrapData);
        $bootstrapData = $this->getRatings($bootstrapData);

        return $bootstrapData;
    }

    /**
     * @param array $bootstrapData
     * @return array
     */
    private function getWatchlist($bootstrapData)
    {
        $list = $bootstrapData['user']
            ->watchlist()
            ->first();

        if ( ! $list) return $bootstrapData;

        $items = $list->getItems(['minimal' => true]);

        $bootstrapData['watchlist'] = [
            'id' => $list->id,
            'items' => $items
        ];

        return $bootstrapData;
    }

    /**
     * @param $bootstrapData
     * @return mixed
     */
    private function getRatings($bootstrapData)
    {
        $bootstrapData['ratings'] = $bootstrapData['user']->ratings()->get();
        return $bootstrapData;
    }
}
