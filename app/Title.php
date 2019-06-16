<?php

namespace App;

use App\Services\Traits\HasCreditableRelation;
use Carbon\Carbon;
use Common\Settings\Settings;
use Common\Tags\Tag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Class Title
 * @property integer $id;
 * @property boolean $allow_update;
 * @property boolean $fully_synced;
 * @property string $type;
 * @property boolean $series_ended;
 * @property Carbon $updated_at;
 * @property Carbon $release_date;
 * @property-read Collection $keywords;
 * @property-read Collection $genres;
 * @property-read Collection $videos;
 * @property-read Collection $images;
 * @property-read Collection|Season[] $seasons;
 * @property-read int $seasons_count;
 * @property Season $season;
 * @property integer $season_count;
 * @property string|null $tmdb_id;
 */
class Title extends Model
{
    use HasCreditableRelation;

    const MOVIE_TYPE = 'movie';
    const SERIES_TYPE = 'series';
    const TITLE_TYPE = 'title';

    protected $guarded = ['id', 'type'];
    protected $dates = ['release_date'];
    protected $appends = ['rating', 'type'];

    public $hidden = [
        'imdb_rating',
        'imdb_votes_num',
        'tmdb_vote_average',
        'local_vote_average',
        'tmdb_vote_count',
        'mc_user_score',
        'mc_critic_score'
    ];

    protected $casts = [
        'id' => 'integer',
        'allow_update' => 'boolean',
        'series_ended' => 'boolean',
        'is_series' => 'boolean',
        'tmdb_vote_count' => 'integer',
        'runtime' => 'integer',
        'views' => 'integer',
        'season_count' => 'integer',
        'episode_count' => 'integer',
        'year' => 'integer',
        'popularity' => 'integer',
        'tmdb_vote_average' => 'float',
        'local_vote_average' => 'float',
        'fully_synced' => 'boolean',
        'adult' => 'boolean',
    ];

    public function needsUpdating($forceAutomation = false)
    {
        // auto update disabled in settings
        if ( ! $forceAutomation && ! app(Settings::class)->get('content.automation')) return false;

        // title was never synced from external site
        if ( ! $this->exists || ($this->allow_update && ! $this->fully_synced)) return true;

        // series ended and fully synced
        //if ($this->series_ended) return false;

        if ( ! $this->release_date) return true;

        // title was released over a month ago, no need to sync anymore
        //if ($this->release_date->lessThan(Carbon::now()->subMonth())) return false;

        // sync every week
        return ($this->allow_update && $this->updated_at->lessThan(Carbon::now()->subWeek()));
    }

    /**
     * @param Collection $titles
     * @param string $uniqueKey
     * @return Collection
     */
    public function insertOrRetrieve(Collection $titles, $uniqueKey)
    {
        // TODO: refactor this into a trait so can use in both title and person models
        // (and possible elsewhere (genres, tags) etc)

        // model "guarded" property will not work here, need to
        // manually unset props that have no corresponding db columns
        $titles = $titles->map(function($value) {
            unset($value['relation_data']);
            unset($value['id']);
            unset($value['type']);
            return $value;
        });

        $existing = $this->whereIn($uniqueKey, $titles->pluck($uniqueKey))->get();

        $new = $titles->filter(function($title) use($existing, $uniqueKey) {
            return !$existing->contains($uniqueKey, $title[$uniqueKey]);
        });

        if ($new->isNotEmpty()) {
            $this->insert($new->toArray());
            return $this->whereIn($uniqueKey, $titles->pluck($uniqueKey))->get();
        } else {
            return $existing;
        }
    }

    public function getTypeAttribute()
    {
        return self::TITLE_TYPE;
    }

    /**
     * @return float
     */
    public function getRatingAttribute()
    {
        return Arr::get($this->attributes, config('common.site.rating_column'));
    }

    public function setReleaseDateAttribute($value)
    {
        $this->attributes['release_date'] = $value;

        if ($this->release_date) {
            $this->attributes['year'] = $this->release_date->year;
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function genres()
    {
        return $this->morphToMany(Tag::class, 'taggable')
            ->where('tags.type', 'genre')
            ->select('id', 'tags.type', 'name', 'display_name');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function keywords()
    {
        return $this->morphToMany(Tag::class, 'taggable')
            ->where('tags.type', 'keyword')
            ->select('id', 'tags.type', 'name', 'display_name');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function countries()
    {
        return $this->morphToMany(Tag::class, 'taggable')
            ->where('tags.type', 'production_country')
            ->select('id', 'tags.type', 'name', 'display_name');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function videos()
    {
        return $this->hasMany(Video::class)
            ->whereNull('episode_id')
            ->where('approved', true)
            ->orderBy('order', 'asc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allVideos()
    {
        return $this->hasMany(Video::class)
            ->orderBy('order', 'desc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function images()
    {
        return $this->morphMany(Image::class, 'model')
            ->select(['id', 'model_id', 'model_type', 'url', 'type', 'source']);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function seasons()
    {
        return $this->hasMany(Season::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function season()
    {
        return $this->hasOne(Season::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function episodes()
    {
        return $this->hasMany(Episode::class);
    }

    /**
     * Get last and next episode to air for this series.
     *
     * @return Collection|Episode[]
     */
    public function getLastAndNextEpisodes()
    {
        $episodes = app(Episode::class)
            ->where('title_id', $this->id)
            ->where('season_number', $this->season_count)
            ->whereBetween('release_date', [Carbon::now()->subWeek(), Carbon::now()->addWeek()])
            ->whereNotNull('poster')
            ->whereNotNull('description')
            ->limit(2)
            ->get();

        if ($episodes->count() !== 2) return null;

        return collect([
            'next_episode' => $episodes->last(),
            'current_episode' => $episodes->first(),
        ]);
    }
}
