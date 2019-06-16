<?php

namespace App\Http\Controllers;

use Common\Core\Controller;
use Illuminate\Http\Request;
use App\Services\Data\Contracts\DataProvider;

class SearchController extends Controller
{
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

    public function search($query)
    {
        $results = app(DataProvider::class)
            ->search($query, $this->request->all());

        $results = $results->map(function($result) {
            if (isset($result['description'])) {
                $result['description'] = str_limit($result['description'], 170);
            }
            return $result;
        });

        return $this->success(['results' => $results]);
    }
}
