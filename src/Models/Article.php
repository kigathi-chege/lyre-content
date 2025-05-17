<?php

namespace Lyre\Content\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lyre\Facet\Concerns\HasFacet;
use Lyre\File\Concerns\HasFile;
use Lyre\File\Http\Resources\File;
use Lyre\Model;

class Article extends Model
{
    use HasFactory, HasFile, HasFacet;

    const ID_COLUMN = 'slug';
    const NAME_COLUMN = 'title';
    const SINGLE_FILE = 'true';
    const ORDER_COLUMN = 'published_at';
    const ORDER_DIRECTION = 'desc';

    protected $with = ['author', 'facetValues'];

    protected array $included = ['read_time', 'featured_image'];

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

    public function getFeaturedImageAttribute()
    {
        return File::make($this->files()->where('mimetype', 'like', 'image/%')->first());
    }

    public function getReadTimeAttribute()
    {
        $text = strip_tags($this->content);
        $wordCount = str_word_count($text);

        // TODO: Kigathi - April 26 2025 - This should be configurable
        $wordsPerMinute = 200;
        $minutes = ceil($wordCount / $wordsPerMinute);
        return $minutes;
    }
}
