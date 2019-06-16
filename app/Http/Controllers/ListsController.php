<?php

namespace App\Http\Controllers;

use App\Listable;
use App\ListModel;
use Auth;
use Common\Core\Controller;
use Common\Database\Paginator;
use DB;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class ListsController extends Controller
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var ListModel
     */
    private $list;

    /**
     * @param Request $request
     * @param ListModel $list
     */
    public function __construct(Request $request, ListModel $list)
    {
        $this->request = $request;
        $this->list = $list;
    }

    /**
     * @return LengthAwarePaginator
     */
    public function index()
    {
        $this->authorize('index', [ListModel::class, Auth::id()]);

        $paginator = (new Paginator($this->list));

        if ($userId = $this->request->get('userId')) {
            $paginator->where('user_id', $userId);
        }

        if ($listIds = $this->request->get('listIds')) {
            $paginator->query()->whereIn('id', explode(',', $listIds));
        }

        if ($excludeSystem = $this->request->get('excludeSystem')) {
            $paginator->where('system', false);
        }

        return $paginator->paginate($this->request->all());
    }

    /**
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        /** @var ListModel $list */
        $list = $this->list->findOrFail($id);

        $this->authorize('show', $list);

        $items = $list->getItems();
        $items = $items->sortBy(
            $this->request->get('sortBy', 'pivot.order'),
            SORT_REGULAR,
            $this->request->get('sortDir') === 'desc'
        )->values();

        $paginator = new LengthAwarePaginator($items, $items->count(), 100);

        return $this->success([
            'list' => $list,
            'items' => $paginator,
        ]);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->authorize('store', ListModel::class);

        $this->validate($this->request, [
            'details.name' => 'required|string|max:100',
            'details.description' => 'nullable|string|max:500',
            'details.public' => 'boolean',
            'auto_update' => 'string',
            'items' => 'array'
        ]);

        $details = $this->request->get('details');

        $list = $this->list->create([
            'name' => $details['name'],
            'description' => $details['description'],
            'auto_update' => Arr::get($details, 'auto_update'),
            'public' => $details['public'],
            'user_id' => Auth::id()
        ]);

       if ($items = $this->request->get('items')) {
           $list->attachItems($items);
       }

        return $this->success(['list' => $list]);
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $list = $this->list->findOrFail($id);

        $this->authorize('store', $list);

        $this->validate($this->request, [
            'details.name' => 'required|string|max:100',
            'details.description' => 'nullable|string|max:500',
        ]);

        $list->fill($this->request->get('details'))->save();

        return $this->success(['list' => $list]);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        $listIds = $this->request->get('listIds');

        $lists = $this->list->whereIn('id', $listIds)
            ->where('system', false)
            ->get();

        $this->authorize('destroy', [ListModel::class, $lists]);

        // make sure system lists can't be deleted
        $listIds = $lists->pluck('id');

        app(Listable::class)->whereIn('list_id', $listIds)->delete();
        $this->list->whereIn('id', $listIds)->delete();

        return $this->success();
    }
}
