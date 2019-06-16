<?php

namespace App\Http\Controllers;

use App\Jobs\IncrementModelViews;
use App\Person;
use App\Services\Data\Contracts\DataProvider;
use App\Services\People\Retrieve\GetPersonCredits;
use App\Services\People\Store\StorePersonData;
use App\Services\Titles\Retrieve\FindOrCreateMediaItem;
use Common\Core\Controller;
use Common\Database\Paginator;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PeopleController extends Controller
{
    /**
     * @var Person
     */
    private $person;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param Person $person
     * @param Request $request
     */
    public function __construct(Person $person, Request $request)
    {
        $this->person = $person;
        $this->request = $request;
    }

    public function index()
    {
        $this->authorize('index', Person::class);

        $paginator = new Paginator($this->person);
        $paginator->where('adult', false);
        $paginator->setDefaultOrderColumns('popularity', 'desc');
        $paginator->with('popularCredits');

        if ($this->request->get('mostPopular')) {
            $paginator->where('popularity', '>', 1);
        }

        $results = $paginator->paginate($this->request->all());

        $results->map(function(Person $person) {
            $person->description = str_limit($person->description, 500);
            $person->setRelation('popular_credits', $person->popularCredits->slice(0, 1));
            return $person;
        });

        return $results;
    }

    public function show($id)
    {
        $this->authorize('show', Person::class);

        $person = app(FindOrCreateMediaItem::class)->execute($id, Person::PERSON_TYPE);

        if ($person->needsUpdating()) {
            $data = app(DataProvider::class)->getPerson($person);
            $person = app(StorePersonData::class)->execute($person, $data);
        }

        $response = array_merge(
            ['person' => $person],
            app(GetPersonCredits::class)->execute($person)
        );

        $this->dispatch(new IncrementModelViews($person));

        return $this->success($response);
    }

    public function store()
    {
        $this->authorize('store', Person::class);

        $person = $this->person->create($this->request->all());

        return $this->success(['person' => $person]);
    }

    public function update($id)
    {
        $this->authorize('update', Person::class);

        $person = $this->person->findOrFail($id);

        $person->fill($this->request->all())->save();

        return $this->success(['person' => $person]);
    }

    public function destroy()
    {
        $this->authorize('destroy', Person::class);

        $ids = $this->request->get('ids');

        $this->person->whereIn('id', $ids)->delete();
        DB::table('creditables')->whereIn('person_id', $ids)->delete();

        return $this->success();
    }
}
