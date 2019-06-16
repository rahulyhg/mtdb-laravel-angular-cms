<?php

namespace App\Http\Controllers;

use App\Listable;
use App\ListModel;
use Common\Core\Controller;
use DB;
use Illuminate\Http\Request;

class ListOrderController extends Controller
{
    /**
     * @var ListModel
     */
    private $list;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param ListModel $list
     * @param Request $request
     */
    public function __construct(ListModel $list, Request $request)
    {
        $this->list = $list;
        $this->request = $request;
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeOrder($id) {

        $list = $this->list->findOrFail($id);

        $this->authorize('update', $list);

        $this->validate($this->request, [
            'itemIds'   => 'array|min:1',
            'itemIds.*' => 'integer'
        ]);

        $queryPart = '';
        foreach($this->request->get('itemIds') as $order => $id) {
            $queryPart .= " when id=$id then $order";
        }

        app(Listable::class)
            ->whereIn('id', $this->request->get('itemIds'))
            ->update(['order' => DB::raw("(case $queryPart end)")]);

        return $this->success();
    }
}
