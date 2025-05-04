<?php

namespace Lyre\Content\Models;

// use App\Models\Harmony\FacetedEntity;
// use App\Models\Harmony\FacetValue;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Lyre\File\Models\Attachment;
use Lyre\File\Models\File;
use Lyre\Model;

class Article extends Model
{
    use HasFactory;

    const ID_COLUMN = 'slug';
    const NAME_COLUMN = 'title';

    const SINGLE_FILE = 'true';

    public function getStatusAttribute()
    {
        return !$this->unpublished && $this->published_at && $this->published_at <= now() ? 'published' : 'unpublished';
    }

    public function scopePublished($query)
    {
        return $query->where('unpublished', '!=', true)->where('published_at', '<=', now());
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function files()
    {
        return $this->hasManyThrough(File::class, Attachment::class, 'attachable_id', 'id', 'id', 'file_id')
            ->where('attachments.attachable_type', self::class);
    }

    // public function facetedEntities(): MorphMany
    // {
    //     return $this->morphMany(FacetedEntity::class, 'entity');
    // }

    // public function facetValues(): HasManyThrough
    // {
    //     return $this->hasManyThrough(
    //         FacetValue::class,
    //         FacetedEntity::class,
    //         'entity_id',        // Foreign key on faceted_entities table...
    //         'id',               // Foreign key on facet_values table...
    //         'id',               // Local key on the articles table...
    //         'facet_value_id'    // Local key on the faceted_entities table...
    //     )->where('entity_type', Article::class);
    // }

    public function getReadTimeAttribute()
    {
        $text = strip_tags($this->content);
        $wordCount = str_word_count($text);

        // TODO: Kigathi - April 26 2025 - This should be configurable
        $wordsPerMinute = 200;
        $minutes = ceil($wordCount / $wordsPerMinute);
        return $minutes;
    }

    public static function includeSerializableColumns()
    {
        return ['read_time'];
    }
}
