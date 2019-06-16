<?php

namespace App;

use Carbon\Carbon;
use Common\Settings\Settings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @property boolean $allow_update;
 * @property boolean $fully_synced;
 * @property integer $tmdb_id;
 * @property Carbon $updated_at;
 * @property-read Collection|\App\Title[] $credits;
 * @property string known_for
 * @method static Person findOrFail($id, $columns = ['*'])
 */
class Person extends Model
{
    const PERSON_TYPE = 'person';

    protected $guarded = ['id', 'relation_data', 'type'];
    protected $appends = ['type'];

    protected $casts = [
        'id' => 'integer',
        'tmdb_id' => 'integer',
        'allow_update' => 'boolean',
        'fully_synced' => 'boolean',
        'adult' => 'boolean',
    ];

    /**
     * @param Collection $people
     * @param string $uniqueKey
     * @return Collection
     */
    public function insertOrRetrieve(Collection $people, $uniqueKey)
    {
        $people = $people->map(function($value) {
            unset($value['relation_data']);
            unset($value['type']);
            unset($value['id']);
            return $value;
        });

        $existing = $this->whereIn($uniqueKey, $people->pluck($uniqueKey))->get();

        $new = $people->filter(function($person) use($existing, $uniqueKey) {
            return !$existing->contains($uniqueKey, $person[$uniqueKey]);
        });

        if ($new->isNotEmpty()) {
            $this->insert($new->toArray());
            return $this->whereIn($uniqueKey, $people->pluck($uniqueKey))->get();
        } else {
            return $existing;
        }
    }

    public function needsUpdating($forceAutomation = false)
    {
        // auto update disabled in settings
        if ( ! $forceAutomation && ! app(Settings::class)->get('content.automation')) return false;

        // person was never synced from external site
        if ( ! $this->exists || ($this->allow_update && ! $this->fully_synced)) return true;

        // sync every week
        return ($this->allow_update && $this->updated_at->lessThan(Carbon::now()->subWeek()));
    }

    public function getTypeAttribute()
    {
        return self::PERSON_TYPE;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function credits()
    {
        return $this->morphedByMany(Title::class, 'creditable')
            ->select('titles.id', 'is_series', 'poster', 'backdrop', 'popularity', 'name', 'year')
            ->withPivot(['id', 'job', 'department', 'order', 'character'])
            ->orderBy('titles.year', 'desc')
            ->where('titles.adult', 0);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function popularCredits()
    {
        return $this->morphedByMany(Title::class, 'creditable')
            ->select('titles.id', 'is_series', 'name', 'year')
            ->orderBy('titles.popularity', 'desc')
            ->where('titles.adult', 0);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function episodeCredits()
    {
        return $this->morphedByMany(Episode::class, 'creditable')
            ->select('episodes.id', 'title_id', 'name', 'year', 'season_number', 'episode_number')
            ->withPivot(['job', 'department', 'order', 'character'])
            ->orderBy('episodes.year', 'desc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function seasonCredits()
    {
        return $this->morphedByMany(Season::class, 'creditable')
        ->select('seasons.id', 'title_id')
        ->withPivot(['job', 'department', 'order', 'character'])
        ->orderBy('seasons.release_date', 'desc');
    }
}
