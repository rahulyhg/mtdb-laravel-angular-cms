<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    const VIDEO_TYPE_EMBED = 'embed';
    const VIDEO_TYPE_DIRECT = 'direct';
    const VIDEO_TYPE_EXTERNAL = 'external';

    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $casts = [
        'negative_votes' => 'integer',
        'positive_votes' => 'integer',
        'order' => 'integer',
        'approved' => 'integer',
        'reports' => 'integer',
        'title_id' => 'integer',
        'id' => 'integer',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function title()
    {
        return $this->belongsTo(Title::class);
    }

    public function ratings()
    {
        return $this->hasMany(VideoRating::class);
    }
}
